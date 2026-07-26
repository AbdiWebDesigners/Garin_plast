<div class="bg-light border-bottom px-4 py-3">

    <h2 class="mb-1">

        <i class="fa <?= $pageIcon ?>"></i>

        <?= htmlspecialchars($pageTitle) ?>

    </h2>

    <?php if (!empty($pageDescription)): ?>

        <small class="text-muted">

            <?= htmlspecialchars($pageDescription) ?>

        </small>

    <?php endif; ?>

</div>