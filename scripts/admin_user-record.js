document.getElementById('searchInput').addEventListener('keyup', applyFilters);

var deptFilter = document.getElementById('deptFilter');
if (deptFilter) {
    deptFilter.addEventListener('change', applyFilters);
}

function applyFilters() {
    var selectedDept = deptFilter ? deptFilter.value.toLowerCase() : 'all';
    var searchInput = document.getElementById('searchInput').value.toLowerCase();
    var tableBody = document.querySelector('table tbody');
    var rows = tableBody.getElementsByTagName('tr');

    for (var i = 0; i < rows.length; i++) {
        var cells = rows[i].getElementsByTagName('td');
        var name = cells[2].textContent.toLowerCase();
        var deptCell = cells[3];
        var deptText = deptCell ? deptCell.textContent.toLowerCase() : '';

        var matchesSearch = name.includes(searchInput);
        var matchesDept = selectedDept === 'all' || deptText.includes(selectedDept);

        if (matchesSearch && matchesDept) {
            rows[i].style.display = '';
        } else {
            rows[i].style.display = 'none';
        }
    }
}

// Add event listener for delete buttons
document.querySelectorAll('.action-btn.deleteBtn').forEach(function (button) {
    button.addEventListener('click', function () {
        var row = this.closest('tr');
        var userId = row.querySelector('td').textContent;
        var department = row.querySelector('td:nth-child(4)').textContent;

        if (confirm('Are you sure you want to delete this user?')) {
            fetch('db/db_delete-user.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ userId: userId, department: department })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        row.remove();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    });
});

// Add event listener for edit buttons
document.querySelectorAll('.action-btn.editBtn').forEach(function (button) {
    button.addEventListener('click', function () {
        alert('Message');
        // Show modal
        document.getElementById('editModal').style.display = 'block';
    });
});

// Close modal when clicking outside or close button
document.getElementById('closeEditModal').addEventListener('click', function () {
    document.getElementById('editModal').style.display = 'none';
});