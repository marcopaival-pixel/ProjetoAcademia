#!/usr/bin/env python3
"""
Bug Surgeon scope guard — blocks file edits outside approved authorization.

Reads Cursor preToolUse hook JSON from stdin.
Exit 0 + {"permission":"allow"} or {"permission":"deny", ...}
Exit 2 = deny (fail-closed when configured).
"""
from __future__ import annotations

import hashlib
import json
import os
import sys
from pathlib import Path

EDIT_TOOLS = {"Write", "StrReplace", "Delete", "EditNotebook"}
EDIT_ALLOWED_STATES = {"APPROVED", "PATCH_APPLIED"}

ROOT = Path.cwd()
SESSION_PATH = ROOT / ".cursor" / "bug-surgeon" / "session.json"
AUTH_PATH = ROOT / ".cursor" / "bug-surgeon" / "authorization.json"


def emit(payload: dict) -> None:
    print(json.dumps(payload, ensure_ascii=False))


def deny(agent_message: str, user_message: str | None = None) -> None:
    out = {"permission": "deny", "agent_message": agent_message}
    if user_message:
        out["user_message"] = user_message
    emit(out)
    sys.exit(2)


def allow() -> None:
    emit({"permission": "allow"})
    sys.exit(0)


def load_json(path: Path) -> dict | None:
    if not path.is_file():
        return None
    try:
        return json.loads(path.read_text(encoding="utf-8"))
    except (json.JSONDecodeError, OSError):
        return None


def normalize_path(raw: str) -> str:
    p = raw.replace("\\", "/").lstrip("./")
    if p.startswith("backend/") or p.startswith(".cursor/") or "/" in p:
        return p
    return p


def extract_tool_info(data: dict) -> tuple[str, dict]:
    tool = (
        data.get("tool_name")
        or data.get("tool")
        or data.get("name")
        or ""
    )
    tool_input = (
        data.get("tool_input")
        or data.get("arguments")
        or data.get("input")
        or {}
    )
    if not isinstance(tool_input, dict):
        tool_input = {}
    return str(tool), tool_input


def extract_file_path(tool: str, tool_input: dict) -> str | None:
    for key in ("path", "file_path", "target_notebook", "notebook_path"):
        if key in tool_input and tool_input[key]:
            return normalize_path(str(tool_input[key]))
    return None


def diff_from_str_replace(tool_input: dict) -> str:
    old = tool_input.get("old_string", "")
    new = tool_input.get("new_string", "")
    return f"- {old}\n+ {new}"


def diff_from_write(tool_input: dict) -> str:
    contents = tool_input.get("contents", "")
    return f"+ {contents}"


def sha256(text: str) -> str:
    return hashlib.sha256(text.encode("utf-8")).hexdigest()


def path_matches_approved(file_path: str, approved_files: list) -> bool:
    norm = normalize_path(file_path)
    for approved in approved_files:
        a = normalize_path(str(approved))
        if norm == a or norm.endswith("/" + a) or a.endswith("/" + norm):
            return True
        # Windows / mixed: compare basename match only as last resort
        if Path(norm).name == Path(a).name and a in norm:
            return True
    return False


def main() -> None:
    try:
        raw = sys.stdin.read()
        data = json.loads(raw) if raw.strip() else {}
    except json.JSONDecodeError:
        allow()

    tool, tool_input = extract_tool_info(data)

    if tool not in EDIT_TOOLS:
        allow()

    session = load_json(SESSION_PATH)
    if not session:
        allow()

    state = str(session.get("state", "")).upper()
    incident = session.get("incident_id", "BUG-????")

    if state not in EDIT_ALLOWED_STATES:
        deny(
            f"Bug Surgeon [{incident}]: estado '{state}' proíbe edição. "
            "Complete investigação, diagnóstico, impacto e obtenha [APROVAR] antes de editar.",
            f"Correção bloqueada: incidente {incident} ainda não está aprovado para patch.",
        )

    auth = load_json(AUTH_PATH)
    if not auth:
        deny(
            f"Bug Surgeon [{incident}]: estado APPROVED sem authorization.json.",
            "Autorização de escopo ausente. Aprove o diff e grave authorization.json.",
        )

    file_path = extract_file_path(tool, tool_input)
    approved_files = auth.get("approved_files") or []

    if file_path and approved_files and not path_matches_approved(file_path, approved_files):
        deny(
            f"Bug Surgeon [{incident}]: arquivo '{file_path}' não está em approved_files.",
            f"Edição bloqueada: somente {approved_files} estão autorizados.",
        )

    if tool == "Delete" and not auth.get("allow_delete_files", False):
        deny(f"Bug Surgeon [{incident}]: exclusão de arquivos não autorizada.")

    approved_hash = auth.get("approved_change_hash")
    if approved_hash and tool in ("StrReplace", "Write"):
        candidate = (
            diff_from_str_replace(tool_input)
            if tool == "StrReplace"
            else diff_from_write(tool_input)
        )
        candidate_hash = sha256(candidate)
        if candidate_hash != approved_hash:
            deny(
                f"Bug Surgeon [{incident}]: alteração não corresponde ao diff aprovado (hash).",
                "A correção atual difere do diff autorizado. Solicite nova aprovação.",
            )

    forbidden = auth.get("forbidden_patterns") or []
    blob = json.dumps(tool_input, ensure_ascii=False)
    for pattern in forbidden:
        if pattern and pattern in blob:
            deny(
                f"Bug Surgeon [{incident}]: padrão proibido detectado: {pattern}",
                "Alteração toca tenancy/auth/schema — fora do escopo aprovado.",
            )

    allow()


if __name__ == "__main__":
    main()
