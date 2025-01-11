
var ctx = document.getElementById('chart').getContext('2d');

var customChart = new Chart(ctx, {
    type: 'line', // Chart type
    data: {
    labels: labelsList,
    datasets: [
        {
            label: 'Users',
            data: usersStatistics,
            borderColor: 'rgba(75, 192, 192, 1)',
            fill: false
        },
        {
            label: 'Posts',
            data: postsStatistics,
            borderColor: 'rgba(255, 99, 132, 1)',
            fill: false
        },
        {
            label: 'Comments',
            data: commentsStatistics,
            borderColor: 'rgba(138, 43, 226, 1)',
            fill: false
        }
        
    ]
    },
    options: {
    responsive: true,
    scales: {
        y: {
        beginAtZero: true
        }
    }
    }
});

document.querySelectorAll('.dashboard-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        let currentTab = document.querySelector('[data-target].bg-gray-800');
        currentTab.classList.remove('bg-gray-800', 'text-white');
        currentTab.classList.add('bg-gray-200');
        this.classList.add('bg-gray-800', 'text-white');
        document.querySelector('[data-section]:not(.hidden)').classList.add('hidden');
        document.querySelector('[data-section="' + this.dataset.target + '"]').classList.remove('hidden');
    });
});