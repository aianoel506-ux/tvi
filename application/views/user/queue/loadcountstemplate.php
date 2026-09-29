
<div class="col-md-2 queCounts" style="border-right: 1px solid #EF582D;">

    <div align="center">
    <h4 class="titleClass">Transactions Counts</h4>
    <hr>
        <h2><?php echo $paymentscount->num_rows(); ?></h2>
        <p>Payments</p>
    <hr>
        <h2><?php echo $legalcounts->num_rows(); ?></h2>
        <p>Legal</p>
    <hr>
        <h2><?php echo $custservcounts->num_rows(); ?></h2>
        <p>Customer Service</p>
    <hr>
        <h2><?php echo $complaintscounts->num_rows(); ?></h2>
        <p>Complaints</p>
    <hr>
    </div>
</div>