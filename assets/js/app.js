const refreshButton = document.getElementById('refreshButton');
const websiteRows = document.getElementById('websiteRows');
const searchInput = document.getElementById('searchInput');
const statusFilter = document.getElementById('statusFilter');
const statTotal = document.getElementById('statTotal');
const statOnline = document.getElementById('statOnline');
const statOffline = document.getElementById('statOffline');
const statAvgResponse = document.getElementById('statAvgResponse');
const statLastChecked = document.getElementById('statLastChecked');
const sidebarLinks = document.querySelectorAll('.sidebar .nav-link');
const currentPage = document.body.dataset.page || window.location.pathname.split('/').pop();

function renderWebsites(lines) {
    if (!websiteRows) return;
    websiteRows.innerHTML = lines.map(site => {
        const statusClass = site.status === 'online' ? 'online' : site.status === 'offline' ? 'offline' : 'unknown';
        const responseTime = site.response_time !== null ? `${site.response_time} ms` : '-';
        const httpCode = site.http_code !== null ? site.http_code : '-';
        const ipAddress = site.ip_address || '-';
        const createdAt = new Date(site.last_checked || site.created_at).toLocaleString('en-US', {
            month: 'short', day: '2-digit', hour: '2-digit', minute: '2-digit'
        });
        return `
            <tr data-id="${site.id}">
                <td>${site.url}</td>
                <td><span class="badge ${statusClass}">${site.status.toUpperCase()}</span></td>
                <td>${responseTime}</td>
                <td>${httpCode}</td>
                <td>${ipAddress}</td>
                <td>${createdAt}</td>
                <td>
                    <form class="delete-form" action="delete_website.php" method="post" onsubmit="return confirmDelete();">
                        <input type="hidden" name="id" value="${site.id}">
                        <button type="submit" class="danger-btn">Delete</button>
                    </form>
                </td>
            </tr>
        `;
    }).join('');
    filterTableRows();
}

function filterTableRows() {
    if (!websiteRows) return;
    const query = searchInput?.value.trim().toLowerCase() || '';
    const status = statusFilter?.value || '';

    websiteRows.querySelectorAll('tr').forEach(row => {
        const text = Array.from(row.querySelectorAll('td'))
            .slice(0, 5)
            .map(cell => cell.textContent.toLowerCase())
            .join(' ');
        const rowStatus = row.querySelector('.badge')?.textContent.trim().toLowerCase() || '';
        const matchesQuery = !query || text.includes(query);
        const matchesStatus = !status || rowStatus === status;
        row.style.display = matchesQuery && matchesStatus ? '' : 'none';
    });
}

function refreshDashboard() {
    if (!statTotal && !statOnline && !statOffline && !statAvgResponse) {
        return window.location.reload();
    }

    fetch('api/monitor.php', { credentials: 'same-origin' })
        .then(resp => resp.json())
        .then(data => {
            if (!data.success) return;
            if (statTotal) statTotal.textContent = data.stats.total;
            if (statOnline) statOnline.textContent = data.stats.online;
            if (statOffline) statOffline.textContent = data.stats.offline;
            if (statAvgResponse) statAvgResponse.textContent = data.stats.avg_response_time !== null ? `${data.stats.avg_response_time} ms` : '-';
            if (statLastChecked) statLastChecked.textContent = data.stats.last_checked ? new Date(data.stats.last_checked).toLocaleString() : 'Pending';
            renderWebsites(data.websites || []);
        })
        .catch(() => console.warn('Unable to refresh dashboard.'));
}

function confirmDelete() {
    return confirm('Are you sure you want to remove this website from monitoring?');
}

function setActiveNavLink() {
    const currentPath = window.location.pathname.split('/').pop();
    sidebarLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href === currentPath) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });
}

window.addEventListener('load', () => {
    setActiveNavLink();
    filterTableRows();
});

if (searchInput) {
    searchInput.addEventListener('input', filterTableRows);
}

if (statusFilter) {
    statusFilter.addEventListener('change', filterTableRows);
}

if (refreshButton) {
    refreshButton.addEventListener('click', refreshDashboard);
}

if (currentPage === 'dashboard.php' || currentPage === 'monitor.php') {
    setInterval(refreshDashboard, 5000);
}

