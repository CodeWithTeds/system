<?php
require_once 'Views/includes/head.php';
require_once 'Views/includes/header.php';
require_once 'Views/includes/navbar.php';
?>

<div class="ml-[250px] pt-24 p-8 bg-gray-50 min-h-screen">
    <div class="mb-6">
        <!-- Header and Stats -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Order Management</h1>
            <div class="flex space-x-3">
                <div class="relative">
                    <input type="text"
                        id="searchInput"
                        placeholder="Search orders..."
                        class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

            </div>
        </div>

        <!-- Order Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg">
                        <svg class="w-8 h-8 text-white transform transition-transform hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Orders</p>
                        <p class="text-2xl font-bold bg-gradient-to-r from-blue-500 to-blue-600 text-transparent bg-clip-text">
                            <?= count($orders) ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-gradient-to-r from-green-500 to-emerald-600 shadow-lg">
                        <svg class="w-8 h-8 text-white transform transition-transform hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Completed Orders</p>
                        <p class="text-2xl font-bold bg-gradient-to-r from-green-500 to-emerald-600 text-transparent bg-clip-text">
                            <?= count(array_filter($orders, function($o) { return $o->display_status === 'completed'; })) ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-gradient-to-r from-red-500 to-rose-600 shadow-lg animate-pulse">
                        <svg class="w-8 h-8 text-white transform transition-transform hover:scale-110 hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Cancelled Orders</p>
                        <p class="text-2xl font-bold bg-gradient-to-r from-red-500 to-rose-600 text-transparent bg-clip-text">
                            <?= count(array_filter($orders, function($o) { return $o->display_status === 'cancelled'; })) ?>
                        </p>
                        <p class="text-sm font-medium text-red-500 mt-1">
                            ₱<?= number_format(array_sum(array_map(function($o) { 
                                return $o->display_status === 'cancelled' ? $o->total_amount : 0; 
                            }, $orders)), 2) ?> lost revenue
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-gradient-to-r from-purple-500 to-indigo-600 shadow-lg">
                        <span class="text-white text-2xl font-bold transform transition-transform hover:scale-110">₱</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Revenue</p>
                        <p class="text-2xl font-bold bg-gradient-to-r from-purple-500 to-indigo-600 text-transparent bg-clip-text">
                            ₱<?= number_format(array_sum(array_map(function($o) { return $o->total_amount; }, $orders)), 2) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[100px]">
                        Image
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[120px]">
                        Order ID
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Customer
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[120px]">
                        Amount
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[100px]">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[180px]">
                        Date
                    </th>
                  
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($orders as $order): ?>
                <tr class="hover:bg-gray-50 transition-colors duration-200">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <img src="<?= htmlspecialchars($order->product_image) ?>" 
                             alt="Product"
                             onerror="this.src='/e-commerce/public/images/placeholder.png'"
                             class="w-12 h-12 object-cover rounded-lg border border-gray-200 shadow-sm">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        #<?= htmlspecialchars($order->id) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?= htmlspecialchars($order->user_email) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                        ₱<?= number_format($order->total_amount, 2) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                            <?= $order->display_status === 'completed' 
                                ? 'bg-green-100 text-green-800' 
                                : 'bg-yellow-100 text-yellow-800' ?>">
                            <?= ucfirst($order->display_status) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?= date('M d, Y, h:i A', strtotime($order->created_at)) ?>
                    </td>
                
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="bg-gray-50 px-6 py-3 flex items-center justify-between border-t border-gray-200">
            <div class="flex-1 flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing <span class="font-medium">5</span> results per page
                    </p>
                </div>
                <div class="flex space-x-2">
                    <button id="prevPage" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        Previous
                    </button>
                    <div id="pageNumbers" class="flex space-x-2">
                        <!-- Page numbers will be inserted here by JavaScript -->
                    </div>
                    <button id="nextPage"
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentPage = 1;
    let totalPages = 1;
    let itemsPerPage = 5;
    let ordersData = <?= json_encode($orders) ?>;

    document.addEventListener('DOMContentLoaded', function() {
        displayOrdersPage();

        document.getElementById('prevPage').addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                displayOrdersPage();
            }
        });

        document.getElementById('nextPage').addEventListener('click', () => {
            if (currentPage < totalPages) {
                currentPage++;
                displayOrdersPage();
            }
        });
    });

    function displayOrdersPage() {
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const pageData = ordersData.slice(startIndex, endIndex);
        totalPages = Math.ceil(ordersData.length / itemsPerPage);

        // Update table content
        const tbody = document.querySelector('tbody');
        tbody.innerHTML = '';

        pageData.forEach(order => {
            const row = `
            <tr class="hover:bg-gray-50 transition-colors duration-200">
                <td class="px-6 py-4 whitespace-nowrap">
                    <img src="${order.product_image}" 
                         alt="Product"
                         onerror="this.src='/e-commerce/public/images/placeholder.png'"
                         class="w-12 h-12 object-cover rounded-lg border border-gray-200 shadow-sm">
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    #${order.id}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${order.user_email}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                    ₱${Number(order.total_amount).toFixed(2)}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${getStatusClass(order.display_status)}">
                        ${order.display_status.charAt(0).toUpperCase() + order.display_status.slice(1)}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${new Date(order.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                </td>
               
            </tr>
        `;
            tbody.insertAdjacentHTML('beforeend', row);
        });

        updatePaginationButtons();
        updatePageNumbers();
    }

    function getStatusClass(status) {
        switch (status) {
            case 'completed':
                return 'bg-green-100 text-green-800';
            case 'processing':
                return 'bg-blue-100 text-blue-800';
            case 'pending':
                return 'bg-yellow-100 text-yellow-800';
            case 'cancelled':
                return 'bg-red-100 text-red-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    function updatePaginationButtons() {
        const prevButton = document.getElementById('prevPage');
        const nextButton = document.getElementById('nextPage');

        prevButton.disabled = currentPage === 1;
        nextButton.disabled = currentPage === totalPages;
    }

    function updatePageNumbers() {
        const pageNumbers = document.getElementById('pageNumbers');
        pageNumbers.innerHTML = '';

        for (let i = 1; i <= totalPages; i++) {
            const button = document.createElement('button');
            button.className = `px-3 py-1 text-sm rounded-lg ${
            i === currentPage 
            ? 'bg-blue-500 text-white' 
            : 'bg-white border border-gray-200 hover:bg-gray-50'
        }`;
            button.textContent = i;
            button.addEventListener('click', () => {
                currentPage = i;
                displayOrdersPage();
            });
            pageNumbers.appendChild(button);
        }
    }

    function viewOrder(orderId) {
        window.location.href = `/e-commerce/admin/orders/${orderId}`;
    }

    function updateStatus(orderId) {
        const newStatus = prompt('Enter new status (pending/completed/failed):');
        if (!newStatus) return;

        fetch('/e-commerce/admin/orders/update-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    order_id: orderId,
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Status updated successfully');
                    location.reload();
                } else {
                    alert('Error updating status');
                }
            });
    }

    function deleteOrder(orderId) {
        if (!confirm('Are you sure you want to delete this order?')) return;

        fetch('/e-commerce/admin/orders/delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    order_id: orderId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Order deleted successfully');
                    location.reload();
                } else {
                    alert('Error deleting order');
                }
            });
    }

    function assignDriver(orderId) {
        // Add your driver assignment logic here
    }

    // Add status filter functionality
    document.getElementById('statusFilter').addEventListener('change', function(e) {
        const status = e.target.value.toLowerCase();
        addressData = <?= json_encode($orders) ?>.filter(order =>
            status === '' || order.display_status.toLowerCase() === status
        );
        currentPage = 1;
        displayOrdersPage();
    });

    // Add search functionality
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        addressData = <?= json_encode($orders) ?>.filter(order =>
            order.user_email.toLowerCase().includes(searchTerm) ||
            order.id.toString().includes(searchTerm)
        );
        currentPage = 1;
        displayOrdersPage();
    });
</script>

<?php require_once 'Views/includes/scripts.php'; ?>