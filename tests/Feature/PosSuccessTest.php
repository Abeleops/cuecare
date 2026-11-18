<?php

it('shows the completed transaction code in the success dialog', function (): void {
    $this->withSession(['completed_transaction_code' => 'TRX-000042']);
    $this->view('pos.modals.success')
        ->assertSee('Sale completed')
        ->assertSee('TRX-000042');
});

it('does not show the dialog without a completed sale', function (): void {
    $this->view('pos.modals.success')->assertDontSee('Sale completed');
});
