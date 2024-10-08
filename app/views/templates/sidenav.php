<div class="col-md-4">
    <div class="card mb-2">
        <div class="card-header">Dashboard</div>
        <div class="card-body">
            <div class="list-group-flush">
                <a href="<?=site_url();?>" class="list-group-item"><i class="bi bi-card-checklist"></i> Dashboard</a>
                <?php if(! is_teacher(get_user_id())):?>
                    <a href="<?=site_url('camera/take-picture');?>" class="list-group-item"><i class="bi bi-camera"></i> Take Selfie</a>
                <?php endif;?>
                <a href="#" data-bs-toggle="modal" data-bs-target="#updateProfile" class="list-group-item"><i class="bi bi-person-lines-fill"></i> Update Profile</a>
                <a href="#" data-bs-toggle="modal" data-bs-target="#updatePassword" class="list-group-item"><i class="bi bi-pencil-square"></i> Change Password</a>
            </div>
        </div>
    </div>
</div>