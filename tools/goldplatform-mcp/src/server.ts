import express from 'express';
import { randomUUID } from 'node:crypto';
import { McpServer } from '@modelcontextprotocol/sdk/server/mcp.js';
import { StreamableHTTPServerTransport } from '@modelcontextprotocol/sdk/server/streamableHttp.js';
import { isInitializeRequest } from '@modelcontextprotocol/sdk/types.js';
import { loadConfig } from './config.js';
import { getProjectStatus } from './tools/project-status.js';

const config = loadConfig();
const app = express();
app.use(express.json({ limit: '1mb' }));

const transports = new Map<string, StreamableHTTPServerTransport>();

function createServer(): McpServer {
  const server = new McpServer({
    name: 'goldplatform-developer-mcp',
    version: '0.1.0',
  });

  server.registerTool(
    'project_status',
    {
      title: 'GoldPlatform Project Status',
      description: 'Use this when you need a read-only snapshot of the approved GoldPlatform working copy, including Git, Docker Compose, and Laravel environment status.',
      inputSchema: {},
      annotations: {
        readOnlyHint: true,
        destructiveHint: false,
        idempotentHint: true,
        openWorldHint: false,
      },
    },
    async () => {
      const result = await getProjectStatus(config);
      return {
        structuredContent: result,
        content: [{ type: 'text', text: result.summary }],
        isError: !result.ok,
      };
    },
  );

  return server;
}

app.get('/healthz', (_request, response) => {
  response.json({ ok: true, service: 'goldplatform-developer-mcp', version: '0.1.0' });
});

app.post('/mcp', async (request, response) => {
  const sessionId = request.header('mcp-session-id');
  let transport = sessionId ? transports.get(sessionId) : undefined;

  if (!transport && isInitializeRequest(request.body)) {
    transport = new StreamableHTTPServerTransport({
      sessionIdGenerator: () => randomUUID(),
      onsessioninitialized: (newSessionId) => {
        transports.set(newSessionId, transport!);
      },
    });

    transport.onclose = () => {
      if (transport?.sessionId) {
        transports.delete(transport.sessionId);
      }
    };

    const server = createServer();
    await server.connect(transport);
  }

  if (!transport) {
    response.status(400).json({
      jsonrpc: '2.0',
      error: { code: -32000, message: 'Missing or invalid MCP session.' },
      id: null,
    });
    return;
  }

  await transport.handleRequest(request, response, request.body);
});

app.get('/mcp', async (request, response) => {
  const sessionId = request.header('mcp-session-id');
  const transport = sessionId ? transports.get(sessionId) : undefined;
  if (!transport) {
    response.status(400).send('Invalid MCP session.');
    return;
  }
  await transport.handleRequest(request, response);
});

app.delete('/mcp', async (request, response) => {
  const sessionId = request.header('mcp-session-id');
  const transport = sessionId ? transports.get(sessionId) : undefined;
  if (!transport) {
    response.status(400).send('Invalid MCP session.');
    return;
  }
  await transport.handleRequest(request, response);
});

app.listen(config.port, config.host, () => {
  console.log(`GoldPlatform Developer MCP listening on http://${config.host}:${config.port}/mcp`);
});
