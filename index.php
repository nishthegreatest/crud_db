<?php

require_once 'src/UserController.php';

$controller = new UserController();
$method = $_SERVER['REQUEST_METHOD'];
$isApi = isset($_GET['api']) && $_GET['api'] === '1';

function send_json_headers() {
    header("Content-Type: application/json");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Methods: GET, POST, PUT");
}

if ($isApi) {
    send_json_headers();

    if ($method === 'GET') {
        $controller->index();
    } else if ($method === 'POST') {
        $controller->store();
    } else if ($method === 'PUT') {
        $controller->update();
    } else {
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
    }
    exit;
}

if ($method !== 'GET') {
    http_response_code(405);
    echo "Method not allowed";
    exit;
}
?>
<!doctype html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Studio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.12/dist/full.min.css" rel="stylesheet" type="text/css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --app-bg: linear-gradient(135deg, #f0f7ff 0%, #f6fbff 45%, #f0fff7 100%);
            --app-text: #0f172a;
            --app-card: #ffffff;
            --app-border: #e2e8f0;
            --app-muted: #64748b;
            --app-accent: #2563eb;
        }

        body.app-surface {
            background: var(--app-bg);
            color: var(--app-text);
        }

        .app-card {
            background: var(--app-card);
            border-color: var(--app-border);
            box-shadow: 0 20px 40px -30px rgba(15, 23, 42, 0.4);
        }

        .app-muted {
            color: var(--app-muted);
        }

        .app-accent {
            color: var(--app-accent);
        }

        .app-pagination {
            color: var(--app-text);
            border: 1px solid var(--app-border);
            background: transparent;
        }

        .app-pagination:hover {
            background: rgba(148, 163, 184, 0.12);
        }

        .app-pagination-active {
            background: var(--app-accent);
            color: #ffffff;
            border: none;
        }

        .app-pagination-active:hover {
            background: var(--app-accent);
        }

        .app-pagination:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
</head>
<body class="min-h-screen app-surface">
    <main class="mx-auto flex min-h-screen max-w-6xl flex-col gap-8 px-4 py-8 sm:gap-10 sm:py-12 font-['Space_Grotesk']">
        <section class="flex flex-wrap items-center justify-between gap-3">
            <span class="text-xs font-semibold uppercase tracking-[0.35em] app-accent">User Studio</span>
        </section>

        <div class="space-y-2">
        <section class="rounded-2xl border p-4 app-card sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-yellow-600">Directory</h2>
                    <p class="text-xs app-muted sm:text-sm" id="status">Ready when you are.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <span class="badge badge-secondary" id="count-pill">0 users</span>
                    <button class="btn btn-primary btn-sm sm:btn-md" type="button" id="add-btn">Add user</button>
                </div>
            </div>

                <div class="mt-5 overflow-x-auto">
                    <table class="table table-zebra text-xs sm:text-sm md:text-base min-w-[720px]">
                    <thead>
                        <tr>
                            <th class="text-xs uppercase tracking-widest app-muted">ID</th>
                            <th class="text-xs uppercase tracking-widest app-muted">Full name</th>
                            <th class="text-xs uppercase tracking-widest app-muted">Email</th>
                            <th class="text-xs uppercase tracking-widest app-muted">Password</th>
                            <th class="text-xs uppercase tracking-widest app-muted">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="user-tbody"></tbody>
                </table>
            </div>
        </section>
        <div class="flex flex-wrap items-center justify-center gap-2 sm:justify-end" id="pagination"></div>
        </div>
    </main>

    <dialog id="user-modal" class="modal">
        <div class="modal-box w-[95vw] max-w-2xl">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" aria-label="Close">x</button>
            </form>
            <div class="space-y-2">
                <h3 class="text-lg font-semibold" id="modal-title">New / Edit user</h3>
                <p class="text-sm app-muted">Add or adjust user details and save to update the table.</p>
            </div>
            <form class="mt-4 space-y-4" id="user-form">
                <input type="hidden" id="user-id" name="id">
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="form-control">
                        <div class="label">
                            <span class="label-text app-muted">Full name</span>
                        </div>
                        <input class="input input-bordered w-full" type="text" id="name" name="name" placeholder="Ada Lovelace" required>
                    </label>
                    <label class="form-control">
                        <div class="label">
                            <span class="label-text app-muted">Email</span>
                        </div>
                        <input class="input input-bordered w-full" type="email" id="email" name="email" placeholder="ada@analytical.engine" required>
                    </label>
                    <label class="form-control sm:col-span-2">
                        <div class="label">
                            <span class="label-text app-muted">Password</span>
                        </div>
                        <input class="input input-bordered w-full" type="password" id="password" name="password" placeholder="********" required>
                    </label>
                </div>
                <div class="flex flex-wrap gap-3">
                    <button class="btn btn-primary" type="submit" id="submit-btn">Create user</button>
                    <button class="btn btn-ghost" type="button" id="reset-btn">Clear</button>
                </div>
            </form>
        </div>
    </dialog>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="app.js"></script>
</body>
</html>
