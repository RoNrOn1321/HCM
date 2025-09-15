<?php
// Include authentication helper and require proper authentication
require_once __DIR__ . '/../includes/auth_helper.php';
requireAuth();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Dependents - HCM System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1b68ff',
                        secondary: '#6c757d',
                        success: '#3ad29f',
                        danger: '#dc3545',
                        warning: '#eea303',
                        info: '#17a2b8',
                        light: '#f8f9fa',
                        dark: '#343a40'
                    },
                    fontFamily: {
                        'sans': ['Inter', 'ui-sans-serif', 'system-ui']
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-sans">
    <!-- Top Navigation -->
    <?php include 'includes/header.php'; ?>

    <!-- Sidebar -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="p-4 sm:ml-64">
        <div class="p-4 rounded-lg mt-14">
            <!-- Page Header -->
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Manage Dependents</h1>
                    <p class="text-gray-600">Add, edit, and manage employee dependents and beneficiaries</p>
                </div>
                <button class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center" onclick="openModal('add-dependent-modal')">
                    <i class="fas fa-plus mr-2"></i>
                    Add Dependent
                </button>
            </div>

            <!-- Filters and Search -->
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Search Employee</label>
                        <input
                            type="text"
                            id="employee-search"
                            placeholder="Employee name or ID..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Relationship</label>
                        <select id="relationship-filter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="">All Relationships</option>
                            <option value="Spouse">Spouse</option>
                            <option value="Child">Child</option>
                            <option value="Parent">Parent</option>
                            <option value="Sibling">Sibling</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Beneficiary Status</label>
                        <select id="beneficiary-filter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="">All</option>
                            <option value="1">Beneficiaries Only</option>
                            <option value="0">Non-Beneficiaries</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">HMO Coverage</label>
                        <select id="hmo-filter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="">All</option>
                            <option value="1">HMO Covered</option>
                            <option value="0">Not Covered</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4 flex gap-2">
                    <button id="filter-btn" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-filter mr-2"></i>Apply Filters
                    </button>
                    <button id="reset-btn" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                        <i class="fas fa-refresh mr-2"></i>Reset
                    </button>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <i class="fas fa-users text-primary text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Dependents</p>
                            <p id="total-dependents" class="text-2xl font-bold text-gray-900">-</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <i class="fas fa-shield-alt text-success text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Beneficiaries</p>
                            <p id="total-beneficiaries" class="text-2xl font-bold text-gray-900">-</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <i class="fas fa-heart text-purple-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">HMO Covered</p>
                            <p id="hmo-covered" class="text-2xl font-bold text-gray-900">-</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <i class="fas fa-baby text-warning text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Minors</p>
                            <p id="total-minors" class="text-2xl font-bold text-gray-900">-</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dependents Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Dependents List</h3>
                        <p class="text-sm text-gray-600">Manage employee dependents and beneficiary information</p>
                    </div>
                    <div class="flex gap-2">
                        <button class="bg-gray-100 text-gray-700 px-3 py-1 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                            <i class="fas fa-download mr-1"></i>
                            Export
                        </button>
                        <button class="bg-primary text-white px-3 py-1 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                            <i class="fas fa-print mr-1"></i>
                            Print
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-6 py-3">Employee</th>
                                <th class="px-6 py-3">Dependent Name</th>
                                <th class="px-6 py-3">Relationship</th>
                                <th class="px-6 py-3">Age</th>
                                <th class="px-6 py-3">Gender</th>
                                <th class="px-6 py-3">Beneficiary</th>
                                <th class="px-6 py-3">HMO</th>
                                <th class="px-6 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="dependents-table-body">
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                                    <p>Loading dependents...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-3 border-t border-gray-200 flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        Showing <span id="showing-from">1</span> to <span id="showing-to">10</span> of <span id="total-records">0</span> results
                    </div>
                    <div class="flex space-x-1">
                        <button class="px-3 py-1 bg-gray-200 text-gray-600 rounded hover:bg-gray-300">Previous</button>
                        <button class="px-3 py-1 bg-primary text-white rounded">1</button>
                        <button class="px-3 py-1 bg-gray-200 text-gray-600 rounded hover:bg-gray-300">2</button>
                        <button class="px-3 py-1 bg-gray-200 text-gray-600 rounded hover:bg-gray-300">3</button>
                        <button class="px-3 py-1 bg-gray-200 text-gray-600 rounded hover:bg-gray-300">Next</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Dependent Modal -->
    <div id="add-dependent-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal('add-dependent-modal')"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <form id="dependent-form">
                    <input type="hidden" id="dependent-id" name="dependent_id">
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 id="modal-title" class="text-lg leading-6 font-medium text-gray-900">Add Dependent</h3>
                            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal('add-dependent-modal')">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Employee *</label>
                                <select id="employee-select" name="employee_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                                    <option value="">Select Employee...</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Dependent Name *</label>
                                <input type="text" name="dependent_name" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Relationship *</label>
                                <select name="relationship" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                                    <option value="">Select Relationship</option>
                                    <option value="Spouse">Spouse</option>
                                    <option value="Child">Child</option>
                                    <option value="Parent">Parent</option>
                                    <option value="Sibling">Sibling</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                                <input type="date" name="date_of_birth" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                                <select name="gender" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                                    <option value="">Select Gender</option>
                                    <option value="M">Male</option>
                                    <option value="F">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center">
                                <input type="checkbox" id="is-beneficiary" name="is_beneficiary" value="1" class="mr-2">
                                <label for="is-beneficiary" class="text-sm font-medium text-gray-700">Beneficiary</label>
                            </div>

                            <div id="beneficiary-percentage-container" class="hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Beneficiary Percentage (%)</label>
                                <input type="number" name="beneficiary_percentage" min="1" max="100" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" id="is-hmo-covered" name="is_hmo_covered" value="1" class="mr-2">
                                <label for="is-hmo-covered" class="text-sm font-medium text-gray-700">HMO Coverage</label>
                            </div>
                        </div>

                        <div class="flex justify-end pt-6 border-t border-gray-200 mt-6">
                            <button type="button" class="mr-3 bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400" onclick="closeModal('add-dependent-modal')">Cancel</button>
                            <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                                <i class="fas fa-save mr-2"></i>Save Dependent
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Dependent</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Are you sure you want to delete this dependent? This action cannot be undone.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button id="confirm-delete-btn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">
                        Delete
                    </button>
                    <button onclick="closeModal('delete-modal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for Interactivity -->
    <?php include 'includes/scripts.php'; ?>

    <script>
        // API configuration
        const API_BASE_URL = '/HCM/api';
        let authToken = localStorage.getItem('auth_token');

        // Global variables
        let currentDependents = [];
        let filteredDependents = [];
        let employees = [];
        let dependentToDelete = null;

        // API helper function
        async function apiCall(endpoint, options = {}) {
            const headers = {
                'Content-Type': 'application/json',
                ...(authToken && { 'Authorization': `Bearer ${authToken}` })
            };

            const response = await fetch(`${API_BASE_URL}${endpoint}`, {
                ...options,
                headers: { ...headers, ...options.headers }
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'API request failed');
            }

            return data;
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            initializePage();
            initializeEventListeners();
        });

        async function initializePage() {
            try {
                await loadEmployees();
                await loadDependents();
                updateStatistics();
            } catch (error) {
                console.error('Error initializing page:', error);
                showNotification('Error loading data: ' + error.message, 'error');
            }
        }

        function initializeEventListeners() {
            // Filter buttons
            document.getElementById('filter-btn').addEventListener('click', applyFilters);
            document.getElementById('reset-btn').addEventListener('click', resetFilters);

            // Form submission
            document.getElementById('dependent-form').addEventListener('submit', handleFormSubmit);

            // Beneficiary checkbox toggle
            document.getElementById('is-beneficiary').addEventListener('change', function() {
                const container = document.getElementById('beneficiary-percentage-container');
                if (this.checked) {
                    container.classList.remove('hidden');
                    container.querySelector('input').required = true;
                } else {
                    container.classList.add('hidden');
                    container.querySelector('input').required = false;
                    container.querySelector('input').value = '';
                }
            });

            // Search input
            document.getElementById('employee-search').addEventListener('input', applyFilters);
        }

        async function loadEmployees() {
            try {
                const response = await apiCall('/employees.php');
                employees = response.data.employees || [];

                // Populate employee dropdown
                const select = document.getElementById('employee-select');
                select.innerHTML = '<option value="">Select Employee...</option>';

                employees.forEach(employee => {
                    const option = document.createElement('option');
                    option.value = employee.id;
                    option.textContent = `${employee.full_name} (${employee.employee_id})`;
                    select.appendChild(option);
                });

            } catch (error) {
                console.error('Error loading employees:', error);
            }
        }

        async function loadDependents() {
            try {
                const response = await apiCall('/dependents.php');
                currentDependents = response.data.dependents || [];
                filteredDependents = [...currentDependents];
                renderDependentsTable();
                updateStatistics();

            } catch (error) {
                console.error('Error loading dependents:', error);
                showNotification('Error loading dependents: ' + error.message, 'error');
            }
        }

        function applyFilters() {
            const searchTerm = document.getElementById('employee-search').value.toLowerCase();
            const relationship = document.getElementById('relationship-filter').value;
            const beneficiary = document.getElementById('beneficiary-filter').value;
            const hmo = document.getElementById('hmo-filter').value;

            filteredDependents = currentDependents.filter(dependent => {
                const matchesSearch = !searchTerm ||
                    dependent.employee_name.toLowerCase().includes(searchTerm) ||
                    dependent.employee_number.toLowerCase().includes(searchTerm) ||
                    dependent.dependent_name.toLowerCase().includes(searchTerm);

                const matchesRelationship = !relationship || dependent.relationship === relationship;
                const matchesBeneficiary = beneficiary === '' || dependent.is_beneficiary == beneficiary;
                const matchesHMO = hmo === '' || dependent.is_hmo_covered == hmo;

                return matchesSearch && matchesRelationship && matchesBeneficiary && matchesHMO;
            });

            renderDependentsTable();
            updateStatistics();
        }

        function resetFilters() {
            document.getElementById('employee-search').value = '';
            document.getElementById('relationship-filter').value = '';
            document.getElementById('beneficiary-filter').value = '';
            document.getElementById('hmo-filter').value = '';

            filteredDependents = [...currentDependents];
            renderDependentsTable();
            updateStatistics();
        }

        function renderDependentsTable() {
            const tbody = document.getElementById('dependents-table-body');

            if (filteredDependents.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-users text-4xl mb-2"></i>
                            <p>No dependents found</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = '';

            filteredDependents.forEach(dependent => {
                const row = document.createElement('tr');
                row.className = 'bg-white border-b hover:bg-gray-50';

                const beneficiaryBadge = dependent.is_beneficiary == 1
                    ? `<span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">${dependent.beneficiary_percentage}%</span>`
                    : '<span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">No</span>';

                const hmoBadge = dependent.is_hmo_covered == 1
                    ? '<span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">Covered</span>'
                    : '<span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">Not Covered</span>';

                const minorBadge = dependent.is_minor
                    ? ' <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-1.5 py-0.5 rounded">Minor</span>'
                    : '';

                row.innerHTML = `
                    <td class="px-6 py-4">
                        <div>
                            <div class="font-medium text-gray-900">${dependent.employee_name}</div>
                            <div class="text-sm text-gray-500">${dependent.employee_number}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-medium text-gray-900">${dependent.dependent_name}</td>
                    <td class="px-6 py-4">
                        <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded">${dependent.relationship}</span>
                    </td>
                    <td class="px-6 py-4">${dependent.age || 'N/A'}${minorBadge}</td>
                    <td class="px-6 py-4">${dependent.gender || 'N/A'}</td>
                    <td class="px-6 py-4">${beneficiaryBadge}</td>
                    <td class="px-6 py-4">${hmoBadge}</td>
                    <td class="px-6 py-4">
                        <div class="flex space-x-2">
                            <button onclick="editDependent(${dependent.id})" class="text-blue-600 hover:text-blue-900" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteDependent(${dependent.id}, '${dependent.dependent_name}')" class="text-red-600 hover:text-red-900" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                `;

                tbody.appendChild(row);
            });

            // Update pagination info
            document.getElementById('showing-from').textContent = filteredDependents.length > 0 ? '1' : '0';
            document.getElementById('showing-to').textContent = filteredDependents.length;
            document.getElementById('total-records').textContent = filteredDependents.length;
        }

        function updateStatistics() {
            const totalDependents = filteredDependents.length;
            const totalBeneficiaries = filteredDependents.filter(d => d.is_beneficiary == 1).length;
            const hmoCovered = filteredDependents.filter(d => d.is_hmo_covered == 1).length;
            const totalMinors = filteredDependents.filter(d => d.is_minor).length;

            document.getElementById('total-dependents').textContent = totalDependents;
            document.getElementById('total-beneficiaries').textContent = totalBeneficiaries;
            document.getElementById('hmo-covered').textContent = hmoCovered;
            document.getElementById('total-minors').textContent = totalMinors;
        }

        async function handleFormSubmit(e) {
            e.preventDefault();

            const formData = new FormData(e.target);
            const dependentData = {
                employee_id: formData.get('employee_id'),
                dependent_name: formData.get('dependent_name'),
                relationship: formData.get('relationship'),
                date_of_birth: formData.get('date_of_birth'),
                gender: formData.get('gender'),
                is_beneficiary: formData.get('is_beneficiary') ? 1 : 0,
                beneficiary_percentage: formData.get('beneficiary_percentage') || 0,
                is_hmo_covered: formData.get('is_hmo_covered') ? 1 : 0
            };

            const dependentId = formData.get('dependent_id');

            try {
                if (dependentId) {
                    // Update existing dependent
                    dependentData.dependent_id = dependentId;
                    await apiCall('/dependents.php', {
                        method: 'PUT',
                        body: JSON.stringify(dependentData)
                    });
                    showNotification('Dependent updated successfully!', 'success');
                } else {
                    // Create new dependent
                    await apiCall('/dependents.php', {
                        method: 'POST',
                        body: JSON.stringify(dependentData)
                    });
                    showNotification('Dependent added successfully!', 'success');
                }

                closeModal('add-dependent-modal');
                resetForm();
                await loadDependents();

            } catch (error) {
                console.error('Error saving dependent:', error);
                showNotification('Error saving dependent: ' + error.message, 'error');
            }
        }

        async function editDependent(dependentId) {
            try {
                const response = await apiCall(`/dependents.php?dependent_id=${dependentId}`);
                const dependent = response.data;

                // Fill form with dependent data
                document.getElementById('dependent-id').value = dependent.id;
                document.getElementById('employee-select').value = dependent.employee_id;
                document.querySelector('input[name="dependent_name"]').value = dependent.dependent_name;
                document.querySelector('select[name="relationship"]').value = dependent.relationship;
                document.querySelector('input[name="date_of_birth"]').value = dependent.date_of_birth;
                document.querySelector('select[name="gender"]').value = dependent.gender || '';

                const isBeneficiaryCheckbox = document.getElementById('is-beneficiary');
                isBeneficiaryCheckbox.checked = dependent.is_beneficiary == 1;

                if (dependent.is_beneficiary == 1) {
                    document.getElementById('beneficiary-percentage-container').classList.remove('hidden');
                    document.querySelector('input[name="beneficiary_percentage"]').value = dependent.beneficiary_percentage;
                    document.querySelector('input[name="beneficiary_percentage"]').required = true;
                } else {
                    document.getElementById('beneficiary-percentage-container').classList.add('hidden');
                    document.querySelector('input[name="beneficiary_percentage"]').required = false;
                }

                document.getElementById('is-hmo-covered').checked = dependent.is_hmo_covered == 1;

                // Update modal title
                document.getElementById('modal-title').textContent = 'Edit Dependent';

                // Open modal
                openModal('add-dependent-modal');

            } catch (error) {
                console.error('Error loading dependent for edit:', error);
                showNotification('Error loading dependent: ' + error.message, 'error');
            }
        }

        function deleteDependent(dependentId, dependentName) {
            dependentToDelete = dependentId;
            document.querySelector('#delete-modal p').textContent =
                `Are you sure you want to delete ${dependentName}? This action cannot be undone.`;

            openModal('delete-modal');

            document.getElementById('confirm-delete-btn').onclick = async function() {
                try {
                    await apiCall(`/dependents.php?dependent_id=${dependentToDelete}`, {
                        method: 'DELETE'
                    });

                    showNotification('Dependent deleted successfully!', 'success');
                    closeModal('delete-modal');
                    await loadDependents();

                } catch (error) {
                    console.error('Error deleting dependent:', error);
                    showNotification('Error deleting dependent: ' + error.message, 'error');
                }
            };
        }

        function resetForm() {
            document.getElementById('dependent-form').reset();
            document.getElementById('dependent-id').value = '';
            document.getElementById('modal-title').textContent = 'Add Dependent';
            document.getElementById('beneficiary-percentage-container').classList.add('hidden');
            document.querySelector('input[name="beneficiary_percentage"]').required = false;
        }

        // Notification system
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm ${
                type === 'success' ? 'bg-green-100 text-green-800' :
                type === 'error' ? 'bg-red-100 text-red-800' :
                type === 'warning' ? 'bg-yellow-100 text-yellow-800' :
                'bg-blue-100 text-blue-800'
            }`;
            notification.textContent = message;

            document.body.appendChild(notification);

            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 5000);
        }
    </script>
</body>
</html>