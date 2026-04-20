<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

class NixConfirmationAjax
{
    public function list($param = null)
    {
        if (!User::access(1)) return Ajax::error(401, 'Unauthorized');

        $num = isset($_GET['num']) ? Typo::int($_GET['num']) : 10;
        $offset = isset($_GET['offset']) ? Typo::int($_GET['offset']) : 0;
        $q = Typo::cleanX($_GET['q'] ?? '');
        $status = Typo::cleanX($_GET['status'] ?? '');

        $query = Query::table('nix_confirmations');

        if ($q != '') {
            $query->groupWhere(function($qb) use ($q) {
                $qb->where('order_id', 'LIKE', "%{$q}%")
                   ->orWhere('customer_name', 'LIKE', "%{$q}%")
                   ->orWhere('bank_name', 'LIKE', "%{$q}%");
            });
        }

        if ($status != '') {
            $query->where('status', $status);
        }

        $countQuery = clone $query;
        $total = $countQuery->count();

        $confirms = $query->orderBy('date', 'DESC')
            ->limit($num)
            ->offset($offset)
            ->get();

        $rows = [];
        $mod_url = 'index.php?page=mods&mod=nix_confirmation';

        if (!empty($confirms)) {
            foreach ($confirms as $c) {
                $statusBadge = match ($c->status) {
                    'approved' => '<span class="badge bg-success bg-opacity-10 text-success px-3 rounded-pill fw-bold">Approved</span>',
                    'rejected'  => '<span class="badge bg-danger bg-opacity-10 text-danger px-3 rounded-pill fw-bold">Rejected</span>',
                    default     => '<span class="badge bg-warning bg-opacity-10 text-warning px-3 rounded-pill fw-bold">Pending</span>'
                };

                $proofBtn = !empty($c->proof_image)
                    ? '<a href="' . Site::$url . $c->proof_image . '" target="_blank" class="btn btn-sm btn-light border rounded-3"><i class="bi bi-image me-1"></i> View Proof</a>'
                    : '<span class="text-muted small fst-italic">No Proof</span>';

                $actions = '
                <div class="dropdown">
                    <button class="btn btn-light btn-sm rounded-circle border shadow-none" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-4 p-2">
                        <li><a class="dropdown-item rounded-3 small fw-bold text-success" href="' . $mod_url . '&act=approve&id=' . $c->id . '"><i class="bi bi-check-circle me-2"></i> Approve</a></li>
                        <li><a class="dropdown-item rounded-3 small fw-bold text-danger" href="' . $mod_url . '&act=reject&id=' . $c->id . '"><i class="bi bi-x-circle me-2"></i> Reject</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item rounded-3 small fw-bold text-danger" href="' . $mod_url . '&act=delete&id=' . $c->id . '" onclick="return confirm(\'Delete this confirmation?\')"><i class="bi bi-trash me-2"></i> Delete</a></li>
                    </ul>
                </div>';

                $rows[] = [
                    ['content' => '<strong>' . $c->order_id . '</strong><div class="small text-muted">' . Date::local($c->date) . '</div>'],
                    ['content' => $c->customer_name . '<br><small class="text-muted">' . $c->bank_name . '</small>'],
                    ['content' => Nixomers::formatCurrency($c->amount), 'class' => 'text-end fw-bold'],
                    ['content' => $proofBtn, 'class' => 'text-center'],
                    ['content' => $statusBadge, 'class' => 'text-center'],
                    ['content' => $actions, 'class' => 'text-end pe-3'],
                ];
            }
        }

        return Ajax::response([
            'status' => 'success',
            'data' => $rows,
            'total' => $total,
            'limit' => $num,
            'offset' => $offset
        ]);
    }
}
