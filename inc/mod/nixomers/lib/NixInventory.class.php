<?php
/**
 * NixInventory Class
 * Handles stock movements, logging, and consistency
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

class NixInventory
{
    /**
     * Deduct stock for an order
     */
    public static function deduct($productId, $qty, $refId = '', $notes = '')
    {
        return self::move($productId, 'OUT', $qty, $refId, $notes);
    }

    /**
     * Add/Revert stock
     */
    public static function add($productId, $qty, $refId = '', $notes = '')
    {
        return self::move($productId, 'IN', $qty, $refId, $notes);
    }

    /**
     * core move function
     */
    public static function move($productId, $type, $amount, $refId = '', $notes = '')
    {
        $productId = (int) $productId;
        $amount = (int) $amount;
        if ($amount <= 0) return false;

        $currentStock = (int) Posts::getParam('stock', $productId) ?: 0;
        
        if ($type === 'OUT') {
            $newStock = $currentStock - $amount;
        } else {
            $newStock = $currentStock + $amount;
        }

        // Update Post Param (Primary Stock Source)
        if (Posts::existParam('stock', $productId)) {
            Posts::editParam('stock', $newStock, $productId);
        } else {
            Posts::addParam('stock', $newStock, $productId);
        }

        // Log to Nix Inventory Ledger
        return Query::table('nix_inventory')->insert([
            'post_id' => $productId,
            'type' => $type,
            'amount' => $amount,
            'current_stock' => $newStock,
            'reference' => $refId,
            'notes' => $notes,
            'date' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get aggregate stock (currently just from post param)
     */
    public static function getStock($productId)
    {
        return (int) Posts::getParam('stock', $productId) ?: 0;
    }
}
