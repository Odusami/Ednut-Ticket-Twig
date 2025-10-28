// public/js/app.js

// Modal functionality
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    }
}

function openEditModal(ticketId) {
  // 1. Fetch ticket details from the server
  fetch(`api/tickets.php?action=get&ticket_id=${ticketId}`)
    .then(res => {
      if (!res.ok) throw new Error('Network error');
      return res.json();
    })
    .then(data => {
      if (!data.success) {
        showToast(data.message || 'Ticket not found', 'error');
        return;
      }

      const ticket = data.ticket;

      // 2. Fill form fields
      document.getElementById('editTicketId').value = ticket.id;
      document.getElementById('editTitle').value = ticket.title || '';
      document.getElementById('editDescription').value = ticket.description || '';
      document.getElementById('editPriority').value = ticket.priority || 'low';

      // 3. Show modal
      openModal('editTicketModal');
    })
    .catch(err => {
      console.error('Error loading ticket:', err);
      showToast('Failed to load ticket details', 'error');
    });
}



// // Handle edit form submission via AJAX
// document.addEventListener('DOMContentLoaded', function() {
//   const editForm = document.getElementById('editTicketForm');
//   if (!editForm) return;

//   editForm.addEventListener('submit', async function(e) {
//     e.preventDefault();

//     const submitBtn = editForm.querySelector('[type="submit"]');
//     submitBtn.disabled = true;
//     submitBtn.textContent = 'Updating...';

//     const formData = new FormData(editForm);

//     try {
//       const response = await fetch(editForm.action, {
//         method: 'POST',
//         body: formData
//       });

//       const data = await response.json();

//       if (data.success) {
//         if (typeof showToast === 'function') {
//           showToast(data.message || 'Ticket updated successfully', 'success');
//         }

//         closeModal('editTicketModal');

//         setTimeout(() => window.location.reload(), 1000);
//       } else {
//         if (typeof showToast === 'function') {
//           showToast(data.message || 'Failed to update ticket', 'error');
//         }
//       }
//     } catch (err) {
//       console.error(err);
//       if (typeof showToast === 'function') {
//         showToast('Error updating ticket', 'error');
//       }
//     } finally {
//       submitBtn.disabled = false;
//       submitBtn.textContent = 'Update';
//     }
//   });
// });


// Delete ticket function


function deleteTicket(ticketId) {
    if (confirm('Are you sure you want to delete this ticket?')) {
        // Create a form to submit the delete request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'api/tickets.php';
        
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'delete';
        form.appendChild(actionInput);
        
        const ticketIdInput = document.createElement('input');
        ticketIdInput.type = 'hidden';
        ticketIdInput.name = 'ticket_id';
        ticketIdInput.value = ticketId;
        form.appendChild(ticketIdInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto'; // Restore scrolling
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-overlay')) {
        closeModal(e.target.id);
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const openModals = document.querySelectorAll('.modal-overlay[style="display: flex;"]');
        openModals.forEach(modal => {
            closeModal(modal.id);
        });
    }
});

// --- Toast System ---
function showToast(message, type = 'success') {
    let toast = document.getElementById('toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'toast';
        toast.className = `toast toast--${type}`;
        toast.innerHTML = `
            <div class="toast__content">
                <span>${message}</span>
            </div>
            <button class="toast__close" onclick="hideToast()">&times;</button>
        `;
        document.body.appendChild(toast);
    } else {
        toast.className = `toast toast--${type}`;
        toast.querySelector('.toast__content span').textContent = message;
    }

    toast.style.display = 'flex';
    setTimeout(hideToast, 5000);
}

function hideToast() {
    const toast = document.getElementById('toast');
    if (toast) {
        toast.style.display = 'none';
    }
}
