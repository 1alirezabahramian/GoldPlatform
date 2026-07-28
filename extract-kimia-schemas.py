import json

with open("swagger.json", "r", encoding="utf-8-sig") as f:
    swagger = json.load(f)

schemas = swagger.get("components", {}).get("schemas", {})

print("Schema count:", len(schemas))
print()

with open("kimia-schemas-full.json", "w", encoding="utf-8") as f:
    json.dump(schemas, f, ensure_ascii=False, indent=2)

with open("kimia-schemas-full.md", "w", encoding="utf-8") as f:
    for name, schema in schemas.items():
        f.write(f"# {name}\n\n")
        
        if "description" in schema:
            f.write(f"**Description:** {schema['description']}\n\n")
        
        if "required" in schema:
            f.write("**Required:**\n\n")
            for field in schema["required"]:
                f.write(f"- `{field}`\n")
            f.write("\n")
        
        properties = schema.get("properties", {})
        
        if properties:
            f.write("| Field | Type | Format | Nullable | Default |\n")
            f.write("|---|---|---|---|---|\n")
            
            for field, prop in properties.items():
                ref = prop.get("$ref", "")
                
                if ref:
                    field_type = ref.split("/")[-1]
                elif "type" in prop:
                    field_type = prop["type"]
                else:
                    field_type = ""
                
                field_format = prop.get("format", "")
                nullable = prop.get("nullable", "")
                default = prop.get("default", "")
                
                f.write(
                    f"| `{field}` | `{field_type}` | "
                    f"`{field_format}` | `{nullable}` | `{default}` |\n"
                )
            
            f.write("\n")
        
        f.write("---\n\n")

print("Created:")
print("  kimia-schemas-full.json")
print("  kimia-schemas-full.md")
