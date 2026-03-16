<?php
// =============================================
// Shared Pagination Controls (included by AJAX handlers)
// =============================================
?>
<div class="pagination-wrapper px-3">
    <div class="pagination-info">
        Menampilkan <?= $offset + 1 ?>-<?= min($offset + $per_page, $total) ?> dari <?= $total ?> data
    </div>
    <?php if ($total_pages > 1): ?>
    <nav>
        <ul class="pagination mb-0">
            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="javascript:void(0)" onclick="loadData(<?= $page - 1 ?>)"><i class="bi bi-chevron-left"></i></a>
            </li>
            <?php 
            $start_p = max(1, $page - 2);
            $end_p = min($total_pages, $page + 2);
            if ($start_p > 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            for ($i = $start_p; $i <= $end_p; $i++): 
            ?>
            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                <a class="page-link" href="javascript:void(0)" onclick="loadData(<?= $i ?>)"><?= $i ?></a>
            </li>
            <?php endfor; 
            if ($end_p < $total_pages) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            ?>
            <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                <a class="page-link" href="javascript:void(0)" onclick="loadData(<?= $page + 1 ?>)"><i class="bi bi-chevron-right"></i></a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
</div>
