/**
 * Admin dashboard JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeAdmin();
});

function initializeAdmin() {
    // Initialize sidebar active state
    updateActiveNavigation();

    // Initialize data tables
    const adminTables = document.querySelectorAll('.admin-table');
    adminTables.forEach(table => {
        initializeDataTable(table);
    });

    // Initialize modals
    initializeModals();
}

function updateActiveNavigation() {
    const currentAction = new URLSearchParams(window.location.search).get('action') || 'dashboard';
    const navLinks = document.querySelectorAll('.admin-sidebar a');
    
    navLinks.forEach(link => {
        link.classList.remove('active');
        const href = new URL(link.href);
        const action = href.searchParams.get('action') || 'dashboard';
        if (action === currentAction) {
            link.classList.add('active');
        }
    });
}

function initializeDataTable(table) {
    // Add sorting and filtering functionality
    const headers = table.querySelectorAll('th');
    headers.forEach((header, index) => {
        header.style.cursor = 'pointer';
        header.addEventListener('click', function() {
            sortTable(table, index);
        });
    });
}

function sortTable(table, columnIndex) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    rows.sort((a, b) => {
        const aValue = a.querySelectorAll('td')[columnIndex].textContent;
        const bValue = b.querySelectorAll('td')[columnIndex].textContent;
        return aValue.localeCompare(bValue);
    });
    
    rows.forEach(row => tbody.appendChild(row));
}

function initializeModals() {
    // Modal functionality for confirmations
    const deleteButtons = document.querySelectorAll('.btn-danger');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
}

function exportTableAsCSV(tableName) {
    const table = document.querySelector('.admin-table');
    let csv = [];
    
    // Get headers
    const headers = table.querySelectorAll('th');
    const headerRow = [];
    headers.forEach(header => {
        headerRow.push(header.textContent.trim());
    });
    csv.push(headerRow.join(','));
    
    // Get rows
    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        const rowData = [];
        cells.forEach((cell, index) => {
            if (index < cells.length - 1) { // Skip action column
                rowData.push('"' + cell.textContent.trim() + '"');
            }
        });
        csv.push(rowData.join(','));
    });
    
    // Download CSV
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = tableName + '.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}
