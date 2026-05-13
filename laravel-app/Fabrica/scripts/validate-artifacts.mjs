/**
 * Valida artefatos em outputs/<run_id>/*.json:
 * 1) raiz contra schemas/_envelope.json
 * 2) payload contra o schema do tipo (mapeamento por nome de arquivo)
 *
 * Suporta artefatos do System Builder, System Audit Factory e Auto Evolution Factory (ver AGENTS.md).
 *
 * Uso:
 *   npm run validate -- outputs/<run_id>
 *   node scripts/validate-artifacts.mjs outputs/<run_id>
 *   node scripts/validate-artifacts.mjs C:\caminho\absoluto\para\run_id
 */

import fs from "fs";
import path from "path";
import { fileURLToPath } from "url";
import Ajv from "ajv";
import addFormats from "ajv-formats";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const repoRoot = path.resolve(__dirname, "..");
const schemasDir = path.join(repoRoot, "schemas");

/** Nome do arquivo do artefato → schema do payload em schemas/ */
const ARTIFACT_PAYLOAD_SCHEMA = {
  "00-project-inventory.json": "project-inventory.json",
  "01-business-brief.json": "business-brief.json",
  "02-prd.json": "prd.json",
  "03-ux-spec.json": "ux-spec.json",
  "04-architecture.json": "architecture.json",
  "05-database-design.json": "database-design.json",
  "06-backend-spec.json": "backend-spec.json",
  "07-frontend-spec.json": "frontend-spec.json",
  "08-test-plan.json": "test-plan.json",
  "09-security-review.json": "security-review.json",
  "10-performance-review.json": "performance-review.json",
  "11-deployment-runbook.json": "deployment-runbook.json",
  "12-docs-outline.json": "docs-outline.json",
  "13-code-review-report.json": "code-review-report.json",
  "14-release-checklist.json": "release-checklist.json",
  "99-master-plan.json": "master-plan.json",

  /* System Audit Factory — outputs/<audit_run_id>/ */
  "00-audit-scope.json": "audit-scope.json",
  "20-audit-code-quality.json": "audit-code-quality.json",
  "21-audit-architecture.json": "audit-architecture.json",
  "22-audit-performance.json": "audit-performance.json",
  "23-audit-feature-overload.json": "audit-feature-overload.json",
  "24-audit-duplication.json": "audit-duplication.json",
  "25-audit-security.json": "audit-security.json",
  "26-audit-test-coverage.json": "audit-test-coverage.json",
  "99-audit-consolidated-report.json": "audit-consolidated-report.json",

  /* Auto Evolution Factory — outputs/<evolution_run_id>/ */
  "00-evolution-scope.json": "evolution-scope.json",
  "30-evolution-code-analysis.json": "evolution-code-analysis.json",
  "31-evolution-refactor.json": "evolution-refactor.json",
  "32-evolution-bugfix.json": "evolution-bugfix.json",
  "33-evolution-test-generation.json": "evolution-test-generation.json",
  "34-evolution-security-patch.json": "evolution-security-patch.json",
  "35-evolution-performance.json": "evolution-performance.json",
  "99-evolution-run-report.json": "evolution-run-report.json",
};

function readJson(filePath) {
  const text = fs.readFileSync(filePath, "utf8");
  return JSON.parse(text);
}

function main() {
  const rawArg = process.argv[2];
  if (!rawArg) {
    console.error("Uso: node scripts/validate-artifacts.mjs <pasta-run>");
    console.error("Ex.: npm run validate -- outputs/2026-05-13-demo");
    process.exit(1);
  }

  const runDir = path.isAbsolute(rawArg)
    ? rawArg
    : path.join(repoRoot, rawArg);

  if (!fs.existsSync(runDir) || !fs.statSync(runDir).isDirectory()) {
    console.error(`Pasta não encontrada ou não é diretório: ${runDir}`);
    process.exit(1);
  }

  const ajv = new Ajv({ allErrors: true, strict: false });
  addFormats(ajv);

  const envelopeSchema = readJson(path.join(schemasDir, "_envelope.json"));
  const validateEnvelope = ajv.compile(envelopeSchema);

  const payloadValidators = new Map();
  for (const [artifactFile, schemaFile] of Object.entries(ARTIFACT_PAYLOAD_SCHEMA)) {
    const schemaPath = path.join(schemasDir, schemaFile);
    if (!fs.existsSync(schemaPath)) {
      console.error(`Schema ausente: ${schemaPath}`);
      process.exit(1);
    }
    const schema = readJson(schemaPath);
    payloadValidators.set(artifactFile, ajv.compile(schema));
  }

  const entries = fs.readdirSync(runDir, { withFileTypes: true });
  const jsonFiles = entries
    .filter((e) => e.isFile() && e.name.endsWith(".json"))
    .map((e) => e.name)
    .sort();

  if (jsonFiles.length === 0) {
    console.error(`Nenhum .json em: ${runDir}`);
    process.exit(1);
  }

  let failed = false;

  for (const name of jsonFiles) {
    if (!ARTIFACT_PAYLOAD_SCHEMA[name]) {
      console.warn(`Ignorado (nome não mapeado): ${name}`);
      continue;
    }

    const filePath = path.join(runDir, name);
    let data;
    try {
      data = readJson(filePath);
    } catch (e) {
      console.error(`${name}: JSON inválido — ${e.message}`);
      failed = true;
      continue;
    }

    if (!validateEnvelope(data)) {
      console.error(`${name}: envelope inválido`);
      console.error(ajv.errorsText(validateEnvelope.errors, { separator: "\n" }));
      failed = true;
      continue;
    }

    const validatePayload = payloadValidators.get(name);
    if (!validatePayload(data.payload)) {
      console.error(`${name}: payload inválido`);
      console.error(ajv.errorsText(validatePayload.errors, { separator: "\n" }));
      failed = true;
      continue;
    }

    console.log(`OK  ${name}`);
  }

  if (failed) {
    process.exit(1);
  }
}

main();
