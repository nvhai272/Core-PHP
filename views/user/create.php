<?php
    session_status();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Create</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
    body {
        background-color: #f8f9fa;
    }

    .form-container {
        max-width: 500px;
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    }

    .form-label {
        font-weight: 500;
    }

    #avatarPreview {
        max-width: 120px;
        height: 130px;
        border-radius: 8px;
    }

    .avatar-container {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    </style>
</head>

<body>
    <?php include __DIR__ . '/../layout/nav.php'; ?>

    <?php

        $defaultAvatar = "data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAkGBwgHBgkIBwgKCgkLDRYPDQwMDRsUFRAWIB0iIiAdHx8kKDQsJCYxJx8fLT0tMTU3Ojo6Iys/RD84QzQ5OjcBCgoKDQwNGg8PGjclHyU3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3N//AABEIAJQAtQMBIgACEQEDEQH/xAAbAAEAAgMBAQAAAAAAAAAAAAAAAQIEBQYDB//EADEQAQABAwEFBwIFBQAAAAAAAAABAgMRBAUSITFBExQiUWFxkSNTJDNCQ3IVNGKSwf/EABYBAQEBAAAAAAAAAAAAAAAAAAABAv/EABYRAQEBAAAAAAAAAAAAAAAAAAARAf/aAAwDAQACEQMRAD8A+iANIAAAAAAJE26arlW7RTNU+UQCMDOt7Lv1xmqYo93p/R7nS7T8FGtQzruy9RRxiaavZh10VW6t2uN2fUFRKAAAAAAAAAAAAAAAAP8AoPbS6evU3oop4RzmXQabT27FO7RGPXzeOzLHY2InHiq4zLMRUoxCRBGGPq9Lb1NOKoje6SyUSDmNRZq09yaK49peTe7VsdrY34jxU8c+jRNYgAAAAAAAAAAAAACXpZo7S9bo86nm99DP4y17oOjojFMRHRZEJRQAAAFLtO9bqpnlMS5aqN2qafKXVy5nVRHersRyipcHiJQqAAAAAAAAAAAAJeukqinVWqp5RU8kTmJiYmeHEiusicwljaG9F/T01xPHHFkwyAAAAK1zu0zM8ocvdq3r1dUdZb7ad/sNNV51cIc/C4IEoVAAAAAAAAAAAAAAGZs7Vzp7sRVP055+jf0VU1UxVTOYnq5XnwZOj11zTTNM+K35T0SK6PIwrG0NPcpjx7s+UvfvNmP3aflB7KXblNuiaqpxEMS/tLT2v1b3pS1Wt1lzVTiZiKOkQsEbQ1NWpvf4RyY4NIgBAAAAAAAAAAASgzgEoZem0F+/xxuUz1lsbWy7NMRv5rnrmRWjzHU4ebpY0enj9qF+72ft0/CUcv4fM4ejqO72vt0f6nd7X26Pgo5fh5p4dHT93tfbo+Ed2sz+3R8FHM5hDpatHYqjjap+GLe2VZrjNEzRK0aQZWp0N6xx3d6nzhigACAAAAAACUCi1NE1TimJmZ6NxoNnxbiK73irnp0hGydJu0xeuRO9PJs2VRER0ThIgAAAAAAIwkBWaYmMTGWr2hs7P1LEcY50tsiQcnxicTwkbbaukx9a3H8oarC4IAVAAAAB76K12+oijHCJzLwbTYlEZuV9eQNtERTERTwiOicqzzEipyZVCC+TKoQWyZVCC2TKoQTkyqEFspyokgVxFdE01RmJc5qLXZX66PXg6Np9s0bt+muP1QYNegFQAAAAbjZMfh6vdp232T/bz7gz8mVcgLZMqmVFsmVcmQWyZVMgtkyrkyC2TKuTILZMq5AWy122fy7c9c4Z7A2v+Vb/AJINUAAAAAA2myKp3K46RIA2EoAAAAAAAAAAAAABrdr1T9KPWZQA18cgAAAf/9k="; // Ảnh avatar mặc định
        $avatarSrc     = isset($old_data['avatar']) ? '/uploads/images/tmp/avatar/' . htmlspecialchars($old_data['avatar']) : $defaultAvatar;

        // var_dump($avatarSrc);

        // var_dump($old_data['avatar']);
    ?>

    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="form-container">
            <h3 class="text-center mb-0">Create New User</h3>

            <form action="/admin/create-user" method="POST" enctype="multipart/form-data">

                <!-- <input type="text" name="tenAnhCu" id="" class="form-control" hidden value="<?php $oldUpload?>"/> -->
                <input type="hidden" name="tenAnhCu"
                    value="<?php echo isset($old_data['avatar']) ? htmlspecialchars($old_data['avatar']) : ''; ?>">

                <div class="mb-3">
                    <label for="adminName" class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter name"
                        value="<?php echo isset($old_data['name']) ? htmlspecialchars($old_data['name']) : ''; ?>">
                    <?php if (! empty($errors['name'])): ?>
                    <div class="text-danger"><?php echo htmlspecialchars($errors['name']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="adminEmail" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Enter email"
                        value="<?php echo isset($old_data['email']) ? htmlspecialchars($old_data['email']) : ''; ?>">
                    <?php if (! empty($errors['email'])): ?>
                    <div class="text-danger"><?php echo htmlspecialchars($errors['email']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="adminPassword" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter password">
                    <?php if (! empty($errors['password'])): ?>
                    <div class="text-danger"><?php echo htmlspecialchars($errors['password']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="adminPassword" class="form-label">Facebook ID</label>
                    <input type="password" name="facebook_id" class="form-control" placeholder="Enter fb_id">
                    <?php if (! empty($errors['facebook_id'])): ?>
                    <div class="text-danger"><?php echo htmlspecialchars($errors['facebook_id']); ?></div>
                    <?php endif; ?>
                </div>

                <!-- Avatar Upload + Preview -->
                <div class="mb-3">
                    <label for="adminAvatar" class="form-label">Avatar</label>

                    <div class="avatar-container">
                        <input type="file" name="fileAvatar" id="uploadFileAvatar" class="form-control" accept="image/*"
                            onchange="previewAvatar()" />

                        <img id="avatarPreview" src="<?php echo $avatarSrc ?>" alt="Avatar Preview">

                    </div>

                    <div id="fileError" class="text-danger"></div>

                    <?php if (! empty($errors['avatar'])): ?>
                    <div class="text-danger"><?php echo htmlspecialchars($errors['avatar']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="adminRole" class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="1"
                            <?php echo(isset($old_data['status']) && $old_data['status'] == "1") ? 'selected' : ''; ?>>
                            Active</option>
                        <option value="2"
                            <?php echo(isset($old_data['status']) && $old_data['status'] == "2") ? 'selected' : ''; ?>>
                            Baned</option>
                    </select>
                    <?php if (! empty($errors['role_type'])): ?>
                    <div class="text-danger"><?php echo htmlspecialchars($errors['role_type']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success">Create</button>
                    <button type="reset" class="btn btn-secondary" onclick="resetPreview()">Reset</button>
                </div>
            </form>
        </div>
    </div>

    <?php include __DIR__ . '/../layout/footer.php'; ?>

    <script>
    // Hàm kiểm tra kích thước file upload
    function checkFileSize(fileInputId, errorElementId, maxSizeMB) {
        const fileInput = document.getElementById(fileInputId);
        const errorDiv = document.getElementById(errorElementId);
        errorDiv.innerText = ''; // Xoá thông báo lỗi trước

        // Nếu có file được chọn
        if (fileInput.files.length > 0) {
            const file = fileInput.files[0];
            const maxSize = maxSizeMB * 1024 * 1024; // chuyển MB sang bytes
            if (file.size > maxSize) {
                errorDiv.innerText = "File quá lớn! Vui lòng chọn file nhỏ hơn " + maxSizeMB + "MB.";
                return false;
            }
        }
        return true;
    }

    // Sự kiện submit của form
    document.getElementById("adminForm").addEventListener("submit", function(e) {
        if (!checkFileSize("uploadFileAvatar", "fileError", 5)) {
            e.preventDefault(); // Ngăn form submit nếu file quá lớn
            // Bạn có thể reset input nếu muốn
            document.getElementById("uploadFileAvatar").value = "";
        }
    });

    function previewAvatar() {
        const fileInput = document.getElementById('uploadFileAvatar');
    const preview = document.getElementById('avatarPreview');
    const defaultAvatar = "<?php echo $defaultAvatar; ?>"; // Đảm bảo $defaultAvatar được định nghĩa trong PHP

    if (fileInput.files && fileInput.files[0]) {
        const file = fileInput.files[0];

        // Kiểm tra kiểu MIME của file. Nếu không bắt đầu bằng 'image/', hiển thị ảnh mặc định.
        if (!file.type.startsWith('image/')) {
            //  alert("File bạn chọn không phải file ảnh. Vui lòng chọn file ảnh.");
            preview.src = defaultAvatar;
            //  fileInput.value = ""; // Reset input file nếu cần
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    } else {
        preview.src = defaultAvatar;
    }
    }

   
    </script>

</body>

</html>