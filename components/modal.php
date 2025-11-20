<?php
/**
 * Modal Component
 */
?>
<div id="modal-<?php echo $modalId; ?>" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeModal('<?php echo $modalId; ?>')">&times;</span>
        <h2><?php echo htmlspecialchars($title); ?></h2>
        <div class="modal-body">
            <?php echo $content; ?>
        </div>
    </div>
</div>

<script>
function openModal(modalId) {
    document.getElementById('modal-' + modalId).style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById('modal-' + modalId).style.display = 'none';
}

// Close modal when clicking outside of it
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}
</script>

<style>
.modal {
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
    background-color: white;
    margin: 10% auto;
    padding: 20px;
    border: 1px solid #888;
    border-radius: 8px;
    width: 80%;
    max-width: 600px;
    position: relative;
}

.close {
    position: absolute;
    right: 15px;
    top: 10px;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    color: #aaa;
}

.close:hover {
    color: #000;
}

.modal-body {
    margin-top: 20px;
}
</style>
