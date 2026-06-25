<?php
// Shared search + pagination helpers for admin list pages.

function list_query(): string {
    return trim((string) ($_GET['q'] ?? ''));
}

function list_page(): int {
    return max(1, (int) ($_GET['page'] ?? 1));
}

// Runs a paginated, optionally-searched query.
// $countSql/$rowsSql must contain a single "%WHERE%" placeholder for the search clause.
// $searchCols are the columns ORed together with LIKE when a search term is present.
function paginate_query(string $countSql, string $rowsSql, array $searchCols, int $perPage = 15): array {
    $q = list_query();
    $params = [];
    $where = '';
    if ($q !== '' && $searchCols) {
        $clauses = array_map(fn($c) => "$c LIKE ?", $searchCols);
        $where = 'WHERE (' . implode(' OR ', $clauses) . ')';
        foreach ($searchCols as $c) { $params[] = '%' . $q . '%'; }
    }

    $countStmt = db()->prepare(str_replace('%WHERE%', $where, $countSql));
    $countStmt->execute($params);
    $total = (int) $countStmt->fetchColumn();

    $totalPages = max(1, (int) ceil($total / $perPage));
    $page = min(list_page(), $totalPages);
    $offset = ($page - 1) * $perPage;

    $rowsStmt = db()->prepare(str_replace('%WHERE%', $where, $rowsSql) . " LIMIT $perPage OFFSET $offset");
    $rowsStmt->execute($params);

    return [
        'rows' => $rowsStmt->fetchAll(),
        'total' => $total,
        'page' => $page,
        'totalPages' => $totalPages,
        'q' => $q,
    ];
}

// Renders a search input that submits via GET, preserving nothing else (page resets to 1 on search).
function render_search_box(string $placeholder): void {
    $q = htmlspecialchars(list_query());
    ?>
    <form method="get" class="search-box">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
      <input type="text" name="q" value="<?= $q ?>" placeholder="<?= htmlspecialchars($placeholder) ?>">
      <?php if ($q !== ''): ?><a href="?" class="search-clear" aria-label="Clear search">&times;</a><?php endif; ?>
    </form>
    <?php
}

// Renders Prev / page-status / Next, preserving the current search term.
function render_pagination(int $page, int $totalPages): void {
    if ($totalPages <= 1) return;
    $q = list_query();
    $qs = fn(int $p) => '?' . http_build_query(array_filter(['q' => $q, 'page' => $p > 1 ? $p : null]));
    ?>
    <div class="pagination">
      <?php if ($page > 1): ?><a href="<?= $qs($page - 1) ?>" class="btn sm secondary">← Prev</a><?php else: ?><span class="btn sm secondary disabled">← Prev</span><?php endif; ?>
      <span class="pagination-status">Page <?= $page ?> of <?= $totalPages ?></span>
      <?php if ($page < $totalPages): ?><a href="<?= $qs($page + 1) ?>" class="btn sm secondary">Next →</a><?php else: ?><span class="btn sm secondary disabled">Next →</span><?php endif; ?>
    </div>
    <?php
}
