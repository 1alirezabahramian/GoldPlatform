# AP-20 — Admin & Operator Frontend Foundation

## Scope

A standalone Nuxt application was added under `frontend-admin/` because the repository did not contain an existing Vue/Nuxt frontend. The existing Laravel Vite entry was empty and was not repurposed.

## Implemented

- Nuxt 4 scaffold
- Persian RTL document settings
- Responsive Admin/Operator shell
- Permission-filtered navigation
- Typed API envelope client
- Admin dashboard connected to `/api/v1/admin/dashboard`
- Operator dashboard connected to `/api/v1/operator/dashboard`
- Loading and safe generic error states

## Security boundaries

- No financial calculation exists in the frontend.
- Navigation visibility is not treated as authorization; Backend permissions remain authoritative.
- No token or credential is hard-coded.
- Requests use cookies with `credentials: include`.
- No write operation is exposed in this stage.

## Known gaps

- The repository has no confirmed Admin/Operator session bootstrap endpoint returning the authenticated user and permissions.
- Login/OTP UI is therefore not implemented in this stage.
- Dashboard data is shown as a raw safe contract preview until UI cards are finalized against tested API payloads.
- Dependencies and build have not been executed in this environment.

## Required next step

AP-21 must define or reuse a confirmed session bootstrap contract, add route middleware, replace contract previews with tested UI components, and run Nuxt build/typecheck together with Laravel Feature tests.
