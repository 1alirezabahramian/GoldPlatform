import json

with open("swagger.json", "r", encoding="utf-8-sig") as f:
    swagger = json.load(f)

paths = swagger.get("paths", {})

print("Path count:", len(paths))
print()

for path, methods in paths.items():
    print("=" * 80)
    print(path)

    for method, operation in methods.items():
        if method.lower() not in ["get", "post", "put", "delete", "patch"]:
            continue

        print(f"  {method.upper()}")

        if operation.get("operationId"):
            print("    operationId:", operation["operationId"])

        if operation.get("summary"):
            print("    summary:", operation["summary"])

        if operation.get("tags"):
            print("    tags:", ", ".join(operation["tags"]))

        request_body = operation.get("requestBody")
        if request_body:
            content = request_body.get("content", {})
            for content_type, data in content.items():
                schema = data.get("schema", {})
                ref = schema.get("$ref")
                if ref:
                    print("    request:", ref.split("/")[-1])

        responses = operation.get("responses", {})
        for status, response in responses.items():
            content = response.get("content", {})
            for content_type, data in content.items():
                schema = data.get("schema", {})
                ref = schema.get("$ref")
                if ref:
                    print(f"    response {status}:", ref.split("/")[-1])
                elif schema.get("type"):
                    print(f"    response {status}:", schema.get("type"))

print()
print("Done.")
