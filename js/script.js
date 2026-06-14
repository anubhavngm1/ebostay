// ===== SEARCH PACKAGES =====
function searchPackages() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const packageCards = document.querySelectorAll('.package-card');

    packageCards.forEach(card => {
        const packageName = card.querySelector('h3').textContent.toLowerCase();
        const destination = card.querySelector('.destination').textContent.toLowerCase();
        
        if (packageName.includes(searchInput) || destination.includes(searchInput)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// ===== FILTER PACKAGES =====
function filterPackages(category) {
    const buttons = document.querySelectorAll('.filter-btn');
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');

    const packageCards = document.querySelectorAll('.package-card');
    packageCards.forEach(card => {
        if (category === 'all' || card.dataset.category === category) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// ===== BOOK PACKAGE =====
function bookPackage(packageId) {
    const isLoggedIn = document.querySelector('.nav-links a[href="logout.php"]');
    
    if (!isLoggedIn) {
        alert('Please login to book a package');
        window.location.href = 'pages/login.php';
    } else {
        // Open booking modal or redirect to booking page
        window.location.href = 'pages/dashboard.php?action=book&package_id=' + packageId;
    }
}

// ===== CUSTOMIZE TOUR WITH AI =====
document.addEventListener('DOMContentLoaded', function() {
    const customizeForm = document.getElementById('customizeForm');
    if (customizeForm) {
        customizeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            generateAISuggestions();
        });
    }

    const aiForm = document.getElementById('aiForm');
    if (aiForm) {
        aiForm.addEventListener('submit', function(e) {
            e.preventDefault();
            askAIAssistant();
        });
    }
});

function generateAISuggestions() {
    const packageId = document.getElementById('packageSelect').value;
    const budget = document.getElementById('budget').value;
    const requirements = document.getElementById('requirements').value;

    if (!packageId) {
        alert('Please select a package');
        return;
    }

    // Show loading
    const suggestionsDiv = document.getElementById('aiSuggestions');
    suggestionsDiv.style.display = 'block';
    document.getElementById('suggestionsContent').innerHTML = '<p>Loading AI suggestions...</p>';

    // Call API
    fetch('api/gemini-api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=customize_tour&package_id=' + packageId + '&budget=' + budget + '&requirements=' + encodeURIComponent(requirements)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('suggestionsContent').innerHTML = '<pre>' + data.suggestions + '</pre>';
        } else {
            document.getElementById('suggestionsContent').innerHTML = '<p style="color: red;">Error: ' + data.error + '</p>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('suggestionsContent').innerHTML = '<p style="color: red;">Error generating suggestions</p>';
    });
}

function askAIAssistant() {
    const prompt = document.getElementById('aiPrompt').value;

    if (!prompt.trim()) {
        alert('Please enter your question');
        return;
    }

    // Show loading
    const responseDiv = document.getElementById('aiResponse');
    responseDiv.style.display = 'block';
    document.getElementById('responseContent').innerHTML = '<p>Loading AI response...</p>';

    // Call API
    fetch('api/gemini-api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=ask_assistant&prompt=' + encodeURIComponent(prompt)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('responseContent').innerHTML = '<pre>' + data.response + '</pre>';
        } else {
            document.getElementById('responseContent').innerHTML = '<p style="color: red;">Error: ' + data.error + '</p>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('responseContent').innerHTML = '<p style="color: red;">Error getting response</p>';
    });
}

// ===== DASHBOARD NAV =====
function switchTab(tabName) {
    const tabs = document.querySelectorAll('[data-tab]');
    tabs.forEach(tab => tab.style.display = 'none');
    document.querySelector('[data-tab="' + tabName + '"]').style.display = 'block';
}

// ===== ADMIN PANEL FUNCTIONS =====
function showAddPackageForm() {
    const form = `
        <div class="modal">
            <div class="modal-content">
                <span class="close" onclick="this.parentElement.parentElement.remove()">&times;</span>
                <h2>Add New Package</h2>
                <form method="POST" action="api/admin-handler.php">
                    <input type="hidden" name="action" value="add_package">
                    <div class="form-group">
                        <label>Package Name</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Destination</label>
                        <input type="text" name="destination" required>
                    </div>
                    <div class="form-group">
                        <label>Duration (Days)</label>
                        <input type="number" name="duration" required>
                    </div>
                    <div class="form-group">
                        <label>Price (₹)</label>
                        <input type="number" name="price" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Activities (comma-separated)</label>
                        <input type="text" name="activities" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Package</button>
                </form>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', form);
}

function showAddExpenseForm() {
    const form = `
        <div class="modal">
            <div class="modal-content">
                <span class="close" onclick="this.parentElement.parentElement.remove()">&times;</span>
                <h2>Add New Expense</h2>
                <form method="POST" action="api/admin-handler.php">
                    <input type="hidden" name="action" value="add_expense">
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category" required>
                            <option value="transport">Transport</option>
                            <option value="accommodation">Accommodation</option>
                            <option value="food">Food</option>
                            <option value="activities">Activities</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <input type="text" name="description" required>
                    </div>
                    <div class="form-group">
                        <label>Amount (₹)</label>
                        <input type="number" name="amount" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="date" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Expense</button>
                </form>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', form);
}

// ===== SMOOTH SCROLL =====
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth' });
        }
    });
});

// ===== DARK MODE TOGGLE (Optional) =====
function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
    localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
}

// Load dark mode preference
if (localStorage.getItem('darkMode') === 'true') {
    document.body.classList.add('dark-mode');
}
