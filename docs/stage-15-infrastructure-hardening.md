# Stage 15 — Infrastructure Hardening

Implemented:

- production-specific PHP and Nginx images;
- PHP production settings and OPcache;
- production Compose without hard-coded credentials;
- no published MySQL or Redis host ports;
- health checks for PHP, Nginx, MySQL and Redis;
- persistent MySQL, Redis and application storage volumes;
- production environment template with secure defaults;
- runtime production configuration guard;
- CI build, startup, `/up` health and network-exposure validation.

Important boundaries:

- TLS termination is expected at the deployment proxy/load balancer and is not invented in this repository.
- real production secrets are not stored in Git.
- the production validation workflow uses ephemeral test credentials only.
- this stage does not add queue workers or monitoring; those belong to later stages.
