document.getElementById('transactionForm').addEventListener('submit', function(e) {
    const amount = document.querySelector('input[name="amount"]').value;
    if (amount <= 0) {
        alert("Please enter an amount greater than zero.");
        e.preventDefault(); // This stops the form from submitting
    }
});