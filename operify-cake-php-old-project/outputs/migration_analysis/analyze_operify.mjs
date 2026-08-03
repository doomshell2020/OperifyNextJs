import fs from "node:fs/promises";
import path from "node:path";

const root = "D:/operify.in";
const outDir = path.join(root, "outputs/migration_analysis");
await fs.mkdir(outDir, { recursive: true });

async function walk(dir) {
  const results = [];
  try {
    const entries = await fs.readdir(dir, { withFileTypes: true });
    for (const entry of entries) {
      const full = path.join(dir, entry.name);
      if (entry.isDirectory()) results.push(...await walk(full));
      else results.push(full);
    }
  } catch {}
  return results;
}

function rel(file) {
  return path.relative(root, file).replaceAll("\\", "/");
}

function uniq(arr) {
  return [...new Set(arr.filter(Boolean))].sort();
}

function matchAll(text, regex, group = 1) {
  return [...text.matchAll(regex)].map(m => m[group]);
}

function controllerModuleName(file) {
  const base = path.basename(file).replace(/Controller\.php$/i, "");
  return base || path.basename(file, ".php");
}

function templateModuleName(file) {
  const parts = rel(file).split("/");
  const adminIndex = parts.indexOf("Admin");
  if (adminIndex >= 0 && parts[adminIndex + 1]) return parts[adminIndex + 1];
  if (parts[1] === "Template" && parts[2]) return parts[2];
  return "Unknown";
}

const controllerFiles = (await walk(path.join(root, "src/Controller"))).filter(f => f.endsWith(".php"));
const modelFiles = (await walk(path.join(root, "src/Model/Table"))).filter(f => f.endsWith(".php"));
const templateFiles = (await walk(path.join(root, "src/Template"))).filter(f => /\.(ctp|php|txt)$/i.test(f));

const modules = new Map();
function ensure(name) {
  if (!modules.has(name)) {
    modules.set(name, {
      module: name,
      controllers: [],
      actions: [],
      templates: [],
      modelFiles: [],
  loadedModels: [],
      dbTables: [],
      rawSqlFiles: [],
      executeCount: 0,
      uploadFiles: [],
      pdfExcelTemplates: [],
      authAllowActions: [],
      dependencies: [],
    });
  }
  return modules.get(name);
}

for (const file of controllerFiles) {
  const text = await fs.readFile(file, "utf8");
  const name = controllerModuleName(file);
  const m = ensure(name);
  m.controllers.push(rel(file));
  m.actions.push(...matchAll(text, /(?:public\s+)?function\s+([A-Za-z_][A-Za-z0-9_]*)\s*\(/g));
  m.loadedModels.push(...matchAll(text, /loadModel\(['"]([^'"]+)['"]\)/g));
  m.loadedModels.push(...matchAll(text, /TableRegistry::get\(['"]([^'"]+)['"]\)/g));
  const execMatches = matchAll(text, /->execute\s*\(/g, 0);
  m.executeCount += execMatches.length;
  if (execMatches.length || /mysqli_|SELECT |INSERT |UPDATE |DELETE |CREATE DATABASE|SHOW DATABASES/i.test(text)) {
    m.rawSqlFiles.push(rel(file));
  }
  if (/move_uploaded_file|upload|Attachment|addAttachment|WWW_ROOT/i.test(text)) m.uploadFiles.push(rel(file));
  for (const allowList of matchAll(text, /Auth->allow\(\s*\[([\s\S]*?)\]\s*\)/g)) {
    m.authAllowActions.push(...matchAll(allowList, /['"]([^'"]+)['"]/g));
  }
}

for (const file of templateFiles) {
  const name = templateModuleName(file);
  const m = ensure(name);
  m.templates.push(rel(file));
  if (/excel|pdf|report|print|viewpdf|summary/i.test(rel(file))) {
    m.pdfExcelTemplates.push(rel(file));
  }
}

for (const file of modelFiles) {
  const name = path.basename(file).replace(/Table\.php$/i, "").replace(/\s+$/g, "");
  const text = await fs.readFile(file, "utf8");
  const m = ensure(name);
  m.modelFiles.push(rel(file));
  const explicitTables = matchAll(text, /->table\(['"]([^'"]+)['"]\)/g);
  if (explicitTables.length) m.dbTables.push(...explicitTables);
  else m.dbTables.push(name.replace(/([a-z])([A-Z])/g, "$1_$2").toLowerCase());
  m.dependencies.push(...matchAll(text, /belongsTo\(['"]([^'"]+)['"]/g));
  m.dependencies.push(...matchAll(text, /hasMany\(['"]([^'"]+)['"]/g));
  m.dependencies.push(...matchAll(text, /hasOne\(['"]([^'"]+)['"]/g));
  m.dependencies.push(...matchAll(text, /belongsToMany\(['"]([^'"]+)['"]/g));
}

for (const mod of modules.values()) {
  mod.controllers = uniq(mod.controllers);
  mod.actions = uniq(mod.actions);
  mod.templates = uniq(mod.templates);
  mod.modelFiles = uniq(mod.modelFiles);
  mod.loadedModels = uniq(mod.loadedModels);
  mod.dbTables = uniq(mod.dbTables);
  mod.rawSqlFiles = uniq(mod.rawSqlFiles);
  mod.uploadFiles = uniq(mod.uploadFiles);
  mod.pdfExcelTemplates = uniq(mod.pdfExcelTemplates);
  mod.authAllowActions = uniq(mod.authAllowActions);
  mod.dependencies = uniq([...mod.dependencies, ...mod.loadedModels]).filter(d => d !== mod.module);
  mod.score = mod.controllers.length * 5 + mod.actions.length + mod.templates.length + mod.executeCount * 2 + mod.modelFiles.length;
}

const moduleList = [...modules.values()].sort((a, b) => b.score - a.score || a.module.localeCompare(b.module));

const summary = {
  generatedAt: new Date().toISOString(),
  root,
  counts: {
    controllers: controllerFiles.length,
    models: modelFiles.length,
    templates: templateFiles.length,
    modules: moduleList.length,
    reportLikeTemplates: moduleList.reduce((n, m) => n + m.pdfExcelTemplates.length, 0),
    rawSqlControllerFiles: uniq(moduleList.flatMap(m => m.rawSqlFiles)).length,
    mappedDbTables: uniq(moduleList.flatMap(m => m.dbTables)).length,
  },
  modules: moduleList,
};

await fs.writeFile(path.join(outDir, "operify_inventory.json"), JSON.stringify(summary, null, 2));

const topRows = moduleList.slice(0, 60).map(m => [
  m.module,
  m.controllers.length,
  m.actions.length,
  m.modelFiles.length,
  m.templates.length,
  m.pdfExcelTemplates.length,
  m.executeCount,
  m.loadedModels.slice(0, 12).join(", "),
].join(" | "));

const markdown = `# Operify Migration Inventory

Generated from \`${root}\`.

## Counts

- Controllers: ${summary.counts.controllers}
- Table/model files: ${summary.counts.models}
- Templates/views: ${summary.counts.templates}
- Inferred modules: ${summary.counts.modules}
- Report/PDF/Excel-like templates: ${summary.counts.reportLikeTemplates}
- Controller files using raw SQL indicators: ${summary.counts.rawSqlControllerFiles}

## Largest Modules

Module | Controllers | Actions | Models | Templates | Report/PDF/Excel Templates | Raw SQL Calls | Loaded Models
--- | ---: | ---: | ---: | ---: | ---: | ---: | ---
${topRows.join("\n")}
`;

await fs.writeFile(path.join(outDir, "operify_inventory.md"), markdown);
console.log(JSON.stringify(summary.counts, null, 2));
