<?php
require_once __DIR__ . '/inc/config.php';

$loggedIn = function_exists('isLoggedIn') && isLoggedIn();
$primaryLink = $loggedIn ? 'dashboard.php' : 'auth/register.php';
$secondaryLink = $loggedIn ? 'dashboard.php' : 'auth/login.php';
$primaryText = $loggedIn ? 'Open Dashboard' : 'Get Started';
$secondaryText = $loggedIn ? 'Go to App' : 'Login';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ServerCheck - Uptime Monitoring</title>
    <link rel="icon" type="image/png" href="/assets/img/favicon.png">
    <link rel="stylesheet" href="assets/css/landing.css">
</head>

<body>

    <header class="navbar">
        <a href="#" class="brand">
            <span class="brand-icon"><img src="assets/images/servercheck-logo.png" alt="ServerCheck logo"></span>
            <span>ServerCheck</span>
        </a>

        <nav class="nav-links">
            <a href="#features">Features</a>
            <a href="#workflow">How it works</a>
            <a href="#preview">Preview</a>
        </nav>

        <div class="nav-actions">
            <a href="<?= $secondaryLink ?>" class="btn btn-ghost"><?= $secondaryText ?></a>
            <a href="<?= $primaryLink ?>" class="btn btn-primary"><?= $primaryText ?></a>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="hero-content reveal">
                <div class="badge">
                    <span></span>
                    Self-hosted uptime monitoring
                </div>

                <h1>Monitor your websites before they go down.</h1>

                <p>
                    ServerCheck helps you track website uptime, response time,
                    HTTP status, and IP address in one clean monitoring dashboard.
                </p>

                <div class="hero-actions">
                    <a href="<?= $primaryLink ?>" class="btn btn-primary btn-large"><?= $primaryText ?></a>
                    <a href="#preview" class="btn btn-outline btn-large">View Preview</a>
                </div>

                <div class="hero-meta">
                    <div>
                        <strong>Realtime</strong>
                        <span>Status refresh</span>
                    </div>
                    <div>
                        <strong>cURL</strong>
                        <span>Reliable checks</span>
                    </div>
                    <div>
                        <strong>PHP</strong>
                        <span>Native backend</span>
                    </div>
                </div>
            </div>

            <div class="hero-preview reveal">
                <div class="mockup-card">
                    <div class="mockup-top">
                        <div>
                            <span class="dot green"></span>
                            All systems operational
                        </div>
                        <small>Live</small>
                    </div>

                    <div class="mockup-grid">
                        <div class="stat-card">
                            <span>Total Websites</span>
                            <strong>12</strong>
                        </div>
                        <div class="stat-card">
                            <span>Online</span>
                            <strong class="green-text">10</strong>
                        </div>
                        <div class="stat-card">
                            <span>Offline</span>
                            <strong class="red-text">2</strong>
                        </div>
                        <div class="stat-card">
                            <span>Avg Response</span>
                            <strong>184ms</strong>
                        </div>
                    </div>

                    <div class="monitor-list">
                        <div class="monitor-row">
                            <div>
                                <strong>example.com</strong>
                                <span>192.168.1.20</span>
                            </div>
                            <span class="status online">Online</span>
                            <small>132ms</small>
                        </div>

                        <div class="monitor-row">
                            <div>
                                <strong>api.server.dev</strong>
                                <span>104.21.32.1</span>
                            </div>
                            <span class="status online">Online</span>
                            <small>98ms</small>
                        </div>

                        <div class="monitor-row">
                            <div>
                                <strong>old-site.test</strong>
                                <span>Unknown</span>
                            </div>
                            <span class="status offline">Offline</span>
                            <small>Timeout</small>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="stats reveal">
            <div class="mini-card">
                <span>01</span>
                <h3>Realtime Checks</h3>
                <p>Dashboard refreshes automatically so every status stays updated.</p>
            </div>

            <div class="mini-card">
                <span>02</span>
                <h3>Response Time</h3>
                <p>Measure website response time in milliseconds using backend checks.</p>
            </div>

            <div class="mini-card">
                <span>03</span>
                <h3>IP Detection</h3>
                <p>Resolve and display the server IP address for each monitored website.</p>
            </div>

            <div class="mini-card">
                <span>04</span>
                <h3>Cron Automation</h3>
                <p>Run checks automatically in the background with scheduled jobs.</p>
            </div>
        </section>

        <section class="section" id="features">
            <div class="section-heading reveal">
                <span>Features</span>
                <h2>Everything you need to monitor website availability.</h2>
                <p>Built for simple uptime tracking with a clean dashboard and practical monitoring data.</p>
            </div>

            <div class="feature-grid">
                <div class="feature-card reveal">
                    <h3>Uptime Monitoring</h3>
                    <p>Check whether your website is online or offline automatically.</p>
                </div>

                <div class="feature-card reveal">
                    <h3>HTTP Status Code</h3>
                    <p>Track server responses like 200, 301, 404, 500, and timeout errors.</p>
                </div>

                <div class="feature-card reveal">
                    <h3>Monitoring History</h3>
                    <p>Store every check result so you can review uptime and downtime logs.</p>
                </div>

                <div class="feature-card reveal">
                    <h3>Clean Dashboard</h3>
                    <p>Separate pages for overview, monitoring, history, and settings.</p>
                </div>

                <div class="feature-card reveal">
                    <h3>Duplicate Prevention</h3>
                    <p>Prevent the same website from being added repeatedly by one user.</p>
                </div>

                <div class="feature-card reveal">
                    <h3>Responsive UI</h3>
                    <p>Designed to work nicely on desktop, laptop, tablet, and mobile screens.</p>
                </div>
            </div>
        </section>

        <section class="section workflow" id="workflow">
            <div class="section-heading reveal">
                <span>How it works</span>
                <h2>Simple monitoring workflow.</h2>
            </div>

            <div class="steps">
                <div class="step reveal">
                    <span>1</span>
                    <h3>Add your website</h3>
                    <p>Enter the website URL you want to monitor.</p>
                </div>

                <div class="step reveal">
                    <span>2</span>
                    <h3>ServerCheck checks it</h3>
                    <p>The checker script detects status, response time, HTTP code, and IP address.</p>
                </div>

                <div class="step reveal">
                    <span>3</span>
                    <h3>View realtime results</h3>
                    <p>Open the dashboard to see updated monitoring data and history logs.</p>
                </div>
            </div>
        </section>

        <section class="section preview-section" id="preview">
            <div class="section-heading reveal">
                <span>Dashboard Preview</span>
                <h2>A focused monitoring dashboard, not a messy admin panel.</h2>
            </div>

            <div class="dashboard-preview reveal">
                <aside>
                    <div class="side-logo">
                        <img src="assets/images/servercheck-logo.png" alt="ServerCheck logo">
                        <span>ServerCheck</span>
                    </div>
                    <a class="active">Dashboard</a>
                    <a>Monitor</a>
                    <a>History</a>
                    <a>Settings</a>
                </aside>

                <div class="preview-main">
                    <div class="preview-header">
                        <div>
                            <h3>Monitor</h3>
                            <p>Manage monitored websites and realtime status.</p>
                        </div>
                        <button>Add Website</button>
                    </div>

                    <div class="preview-table">
                        <div class="table-head">
                            <span>Website</span>
                            <span>Status</span>
                            <span>IP Address</span>
                            <span>Response</span>
                        </div>

                        <div class="table-row">
                            <span>servercheck.local</span>
                            <span class="status online">Online</span>
                            <span>127.0.0.1</span>
                            <span>76ms</span>
                        </div>

                        <div class="table-row">
                            <span>portfolio.dev</span>
                            <span class="status online">Online</span>
                            <span>172.67.1.2</span>
                            <span>118ms</span>
                        </div>

                        <div class="table-row">
                            <span>legacy-api.test</span>
                            <span class="status offline">Offline</span>
                            <span>-</span>
                            <span>Timeout</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="cta reveal">
            <h2>Start monitoring your websites with ServerCheck.</h2>
            <p>Track uptime, response time, HTTP status, and IP address from one simple dashboard.</p>
            <a href="<?= $primaryLink ?>" class="btn btn-primary btn-large"><?= $primaryText ?></a>
        </section>
    </main>

    <footer>
        <p>© <?= date('Y') ?> ServerCheck. Built with PHP Native and MySQL.</p>
    </footer>

    <script src="assets/js/landing.js"></script>
</body>

</html>