<?php
/**
 * Nixomers Fulfillment Detail View
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

$id = Typo::int($_GET['id'] ?? 0);
$order = Query::table('nix_orders')->where('id', $id)->first();

if (!$order) {
    echo '<div class="alert alert-danger">Order record not found.</div>';
    return;
}

$items = json_decode($order->cart_items ?? '[]', true) ?: [];
$logs = Query::table('nix_order_logs')->where('order_id', $order->order_id)->orderBy('date', 'DESC')->get();

// Header Rendering (Analytics Style)
?>
<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h2 class="fw-black text-dark mb-0">Fulfillment Detail</h2>
        <p class="text-muted mb-0">Order: <?php echo $order->order_id; ?> &bull; Tracking logistics and item verification.</p>
    </div>
    <div class="col-md-6 text-end">
        <button onclick="location.href='index.php?page=mods&mod=nix_fulfillment&sel=management'" class="btn btn-light rounded-pill px-4 border shadow-none fw-bold me-2">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </button>
        <button onclick="window.open('<?php echo Url::ajax("nixomers", "print_label", ["id" => $order->order_id]); ?>', '_blank')" class="btn btn-warning rounded-pill px-4 shadow-sm fw-bold">
            <i class="bi bi-printer me-1"></i> Print Label
        </button>
    </div>
</div>

<?php

?>

<div class="row g-4">
    <div class="col-md-8">
        <!-- Packaging & Items -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-black"><i class="bi bi-list-check me-2"></i> Packing List</h5>
                <span class="badge bg-dark rounded-pill px-3"><?php echo count($items); ?> Items</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Product Name</th>
                            <th class="text-center">Qty</th>
                            <th>Weight</th>
                            <th class="pe-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $pId => $qty): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold"><?php echo Posts::title($pId); ?></div>
                                <div class="small text-muted">SKU: <?php echo Posts::getParam('sku', $pId) ?: '-'; ?></div>
                            </td>
                            <td class="text-center"><strong><?php echo $qty; ?></strong></td>
                            <td><?php echo (float)Posts::getParam('weight', $pId) * $qty; ?> gr</td>
                            <td class="pe-4 text-center">
                                <i class="bi bi-check-circle-fill text-success"></i>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Logistics Logs -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-black"><i class="bi bi-clock-history me-2"></i> Fulfillment Logs</h5>
            </div>
            <div class="card-body">
                <div class="timeline-v2">
                    <?php foreach ($logs as $log): ?>
                    <div class="d-flex mb-4">
                        <div class="flex-shrink-0 mt-1">
                            <span class="badge bg-primary rounded-circle p-2"><i class="bi bi-check2"></i></span>
                        </div>
                        <div class="ms-3">
                            <div class="fw-bold text-dark"><?php echo $log->new_status; ?></div>
                            <div class="small text-muted mb-2"><?php echo date('d M Y, H:i', strtotime($log->date)); ?> &bull; By <?php echo $log->updated_by; ?></div>
                            <?php if ($log->notes): ?>
                            <div class="p-3 bg-light rounded-3 border-start border-4 border-primary small italic">
                                <?php echo nl2br($log->notes ?? ''); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Fulfillment Sidebar -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-primary text-white overflow-hidden">
            <div class="card-body p-4 position-relative">
                <i class="bi bi-box-fill position-absolute bottom-0 end-0 mb-n4 me-n4 opacity-25" style="font-size: 120px;"></i>
                <div class="label-text text-white-50 small fw-bold mb-1">PACKAGING CODE</div>
                <h3 class="fw-black mb-3"><?php echo $order->packaging_code ?: 'NOT ASSIGNED'; ?></h3>
                <div class="divider bg-white bg-opacity-25 my-3"></div>
                <div class="row g-2">
                    <div class="col-6">
                        <div class="small text-white-50">STAGING</div>
                        <div class="fw-bold"><?php echo $order->staging_location ?: '-'; ?></div>
                    </div>
                    <div class="col-6">
                        <div class="small text-white-50">STATUS</div>
                        <div class="fw-bold text-uppercase"><?php echo str_replace('_', ' ', $order->status ?? ''); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer & Shipping Card -->
        <div class="card border-0 shadow-sm rounded-4 shadow-sm">
            <div class="card-body p-4">
                <h6 class="fw-black mb-3 text-uppercase small text-muted"><i class="bi bi-truck me-1"></i> Shipping Data</h6>
                
                <div class="mb-4 text-center p-3 bg-light rounded-4">
                    <div class="display-6 fw-black text-dark"><?php echo strtoupper($order->shipping_courier ?? ''); ?></div>
                    <div class="badge bg-white text-primary border rounded-pill px-3"><?php echo strtoupper($order->shipping_service ?? ''); ?></div>
                </div>

                <div class="info-group mb-3">
                    <div class="small text-muted fw-bold mb-1">RECIPIENT</div>
                    <div class="fw-bold fs-5"><?php echo $order->customer_name; ?></div>
                    <div class="small"><?php echo $order->customer_phone; ?></div>
                </div>

                <div class="info-group">
                    <div class="small text-muted fw-bold mb-1">SHIPPING ADDRESS</div>
                    <div class="p-3 bg-light rounded-3 small">
                        <?php echo nl2br($order->shipping_address ?? ''); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline-v2 { position: relative; padding-left: 20px; border-left: 2px solid #f1f1f1; margin-left: 10px; }
.timeline-v2:before { content: ""; }
</style>
