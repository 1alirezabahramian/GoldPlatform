import http from 'k6/http';
import { check, sleep } from 'k6';

const baseUrl = __ENV.BASE_URL || 'http://127.0.0.1:8080';

export const options = {
  scenarios: {
    load: {
      executor: 'constant-vus',
      vus: 10,
      duration: '20s',
      exec: 'healthProbe',
    },
    stress: {
      executor: 'ramping-vus',
      startTime: '20s',
      stages: [
        { duration: '10s', target: 20 },
        { duration: '15s', target: 40 },
        { duration: '10s', target: 0 },
      ],
      exec: 'healthProbe',
    },
    concurrency: {
      executor: 'shared-iterations',
      startTime: '55s',
      vus: 25,
      iterations: 250,
      maxDuration: '20s',
      exec: 'healthProbe',
    },
  },
  thresholds: {
    http_req_failed: ['rate<0.01'],
    http_req_duration: ['p(95)<750', 'p(99)<1500'],
    checks: ['rate>0.99'],
  },
};

export function healthProbe() {
  const response = http.get(`${baseUrl}/up`, {
    tags: { endpoint: 'health' },
    timeout: '5s',
  });

  check(response, {
    'health endpoint returns 200': (result) => result.status === 200,
  });

  sleep(0.05);
}
