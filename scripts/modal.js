$(document).ready(function() {
    // Handle "View Details" button click for custom modal
    $('#myTable').on('click', '.view-details', function() {
        let sectionId = $(this).data('section-id');
        $.ajax({
            url: 'get_students.php', // Ensure this file exists
            type: 'POST',
            data: { section_id: sectionId },
            success: function(response) {
                $('#studentList').html(response); // Populate modal with student list
                showModal(); // Show custom modal
            },
            error: function(xhr, status, error) {
                $('#studentList').html('<p>Error loading students: ' + error + '</p>');
                showModal(); // Show modal even on error
            }
        });
    });
});

// Custom functions for modal control
function showModal() {
    document.getElementById('studentModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('studentModal').style.display = 'none';
}

// Close modal if clicking outside
document.addEventListener('click', function(event) {
    let modal = document.getElementById('studentModal');
    let modalContent = document.querySelector('.custom-modal-content');
    if (event.target === modal && modal.style.display === 'block') {
        closeModal();
    }
});