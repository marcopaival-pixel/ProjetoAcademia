#!/usr/bin/env python3
"""Blocks mutating shell commands during Bug Surgeon investigation (read-only phase)."""
from __future__ import annotations

import json
import re
import sys
from pathlib import Path

READ_ONLY_STATES = {
    "RECEIVED",
    "INVESTIGATING",
    "DIAGNOSIS_READY",
    "IMPACT_ANALYZED",
    "PATCH_PROPOSED",
    "WAITING_APPROVAL",
}

BLOCKED = re.compile(
    r"(?i)"
    r"(\bgit\s+(commit|push|merge|rebase|checkout\s+-b|reset|clean)\b"
    r"|\bcomposer\s+(require|update|remove)\b"
    r"|\bnpm\s+(install|uninstall|update)\b"
    r"|\bphp\s+artisan\s+(migrate|db:wipe|schema:dump)\b"
    r"|\bdel\s+|rm\s+-rf?\s|Remove-Item\b"
    r"|\bmv\s+|\bmove\s+|ren\s+)",
)

SESSION_PATH = Path.cwd() / ".cursor" / "bug-surgeon" / "session.json"


def main() -> None:
    try:
        raw = sys.stdin.read()
        data = json.loads(raw) if raw.strip() else {}
    except json.JSONDecodeError:
        print(json.dumps({"permission": "allow"}))
        return

    command = str(data.get("command") or data.get("cmd") or "")
    if not command or not SESSION_PATH.is_file():
        print(json.dumps({"permission": "allow"}))
        return

    try:
        session = json.loads(SESSION_PATH.read_text(encoding="utf-8"))
    except (json.JSONDecodeError, OSError):
        print(json.dumps({"permission": "allow"}))
        return

    state = str(session.get("state", "")).upper()
    incident = session.get("incident_id", "BUG-????")

    if state not in READ_ONLY_STATES:
        print(json.dumps({"permission": "allow"}))
        return

    if BLOCKED.search(command):
        print(
            json.dumps(
                {
                    "permission": "deny",
                    "user_message": f"Comando bloqueado: incidente {incident} em fase somente leitura.",
                    "agent_message": (
                        f"Bug Surgeon [{incident}]: estado '{state}' proíbe comandos mutáveis. "
                        "Use somente leitura até [APROVAR]."
                    ),
                },
                ensure_ascii=False,
            )
        )
        sys.exit(2)

    print(json.dumps({"permission": "allow"}))


if __name__ == "__main__":
    main()
