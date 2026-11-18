@if (session('completed_transaction_code'))
    <dialog id="posSuccessDialog" aria-labelledby="posSuccessTitle">
        <h2 id="posSuccessTitle">Sale completed</h2>
        <p>Transaction: <strong>{{ session('completed_transaction_code') }}</strong></p>
        <form method="dialog">
            <button type="submit">Done</button>
        </form>
    </dialog>
@endif
