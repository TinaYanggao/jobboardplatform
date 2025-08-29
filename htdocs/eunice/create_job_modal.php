<form class="modal-content" method="POST" action="create_job.php" enctype="multipart/form-data">
    <div class="modal-header">
        <h5 class="modal-title">Create Job</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <input type="text" class="form-control mb-2" name="title" placeholder="Job Title" required>
        <input type="text" class="form-control mb-2" name="location" placeholder="Location" required>
        <textarea class="form-control mb-2" name="requirements" placeholder="Requirements" required></textarea>
        <select class="form-select mb-2" name="type" required>
            <option value="Full-time">Full-time</option>
            <option value="Part-time">Part-time</option>
            <option value="Remote">Remote</option>
        </select>
        <input type="file" class="form-control" name="job_file" accept=".pdf,.doc,.docx">
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success">Post Job</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    </div>
</form>
