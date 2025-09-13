<?php
session_start();
require_once 'Student.php';
require_once 'ClassModel.php';

if (!isset($_SESSION['students'])) $_SESSION['students'] = [];
if (!isset($_SESSION['classes'])) $_SESSION['classes'] = [];

// Handle add student
if (isset($_POST['add_student']) && !empty($_POST['student_name'])) {
  Student::add($_SESSION['students'], $_POST['student_name']);
  header('Location: index.php'); exit;
}
// Handle add class
if (isset($_POST['add_class']) && !empty($_POST['class_name'])) {
  ClassModel::add($_SESSION['classes'], $_POST['class_name']);
  header('Location: index.php'); exit;
}

$students = Student::getAll($_SESSION['students']);
$classes = ClassModel::getAll($_SESSION['classes']);

// Placeholder for assignments (no backend logic)
$assignments = isset($_SESSION['assignments']) ? $_SESSION['assignments'] : [];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ClassAssign - Simple CRUD</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <h1 class="mb-4">ClassAssign Demo</h1>
  <div class="row g-4">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">Add Student</div>
        <div class="card-body">
          <form method="POST" class="d-flex gap-2">
            <input type="text" name="student_name" class="form-control" placeholder="Student Name" required>
            <button class="btn btn-primary" name="add_student">Add</button>
          </form>
        </div>
      </div>
      <div class="card mt-3">
        <div class="card-header">Student List</div>
        <div class="card-body p-0">
          <ul class="list-group list-group-flush">
            <?php foreach ($students as $i => $s): ?>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <span>#<?= $i+1 ?> - <?= htmlspecialchars($s) ?></span>
                <span>
                  <!-- Edit Button triggers modal -->
                  <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editStudentModal<?= $i ?>">Edit</button>
                  <!-- Delete Button triggers modal -->
                  <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteStudentModal<?= $i ?>">Delete</button>
                </span>
              </li>
              <!-- Edit Student Modal -->
              <div class="modal fade" id="editStudentModal<?= $i ?>" tabindex="-1" aria-labelledby="editStudentLabel<?= $i ?>" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="editStudentLabel<?= $i ?>">Edit Student</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <form>
                        <div class="mb-3">
                          <label class="form-label">Student Name</label>
                          <input type="text" class="form-control" value="<?= htmlspecialchars($s) ?>" disabled>
                        </div>
                        <div class="alert alert-info small">-</div>
                      </form>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      <button type="button" class="btn btn-primary" disabled>Save changes</button>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Delete Student Modal -->
              <div class="modal fade" id="deleteStudentModal<?= $i ?>" tabindex="-1" aria-labelledby="deleteStudentLabel<?= $i ?>" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="deleteStudentLabel<?= $i ?>">Delete Student</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      Are you sure you want to delete <strong><?= htmlspecialchars($s) ?></strong>?<br>
                      <div class="alert alert-info small mt-2 mb-0">-</div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button type="button" class="btn btn-danger" disabled>Delete</button>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
            <?php if (empty($students)): ?>
              <li class="list-group-item text-muted">No students yet.</li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">Add Class</div>
        <div class="card-body">
          <form method="POST" class="d-flex gap-2">
            <input type="text" name="class_name" class="form-control" placeholder="Class Name" required>
            <button class="btn btn-success" name="add_class">Add</button>
          </form>
        </div>
      </div>
      <div class="card mt-3">
        <div class="card-header">Class List</div>
        <div class="card-body p-0">
          <ul class="list-group list-group-flush">
            <?php foreach ($classes as $i => $c): ?>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <span>#<?= $i+1 ?> - <?= htmlspecialchars($c) ?></span>
                <span>
                  <!-- Edit Button triggers modal -->
                  <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editClassModal<?= $i ?>">Edit</button>
                  <!-- Delete Button triggers modal -->
                  <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteClassModal<?= $i ?>">Delete</button>
                </span>
              </li>
              <!-- Edit Class Modal -->
              <div class="modal fade" id="editClassModal<?= $i ?>" tabindex="-1" aria-labelledby="editClassLabel<?= $i ?>" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="editClassLabel<?= $i ?>">Edit Class</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <form>
                        <div class="mb-3">
                          <label class="form-label">Class Name</label>
                          <input type="text" class="form-control" value="<?= htmlspecialchars($c) ?>" disabled>
                        </div>
                        <div class="alert alert-info small">-</div>
                      </form>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      <button type="button" class="btn btn-primary" disabled>Save changes</button>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Delete Class Modal -->
              <div class="modal fade" id="deleteClassModal<?= $i ?>" tabindex="-1" aria-labelledby="deleteClassLabel<?= $i ?>" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="deleteClassLabel<?= $i ?>">Delete Class</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      Are you sure you want to delete <strong><?= htmlspecialchars($c) ?></strong>?<br>
                      <div class="alert alert-info small mt-2 mb-0">-</div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button type="button" class="btn btn-danger" disabled>Delete</button>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
            <?php if (empty($classes)): ?>
              <li class="list-group-item text-muted">No classes yet.</li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!-- Assign Student to Class UI -->
  <div class="row g-4 mt-4">
    <div class="col-md-8 mx-auto">
      <div class="card">
        <div class="card-header">Assign Student to Class</div>
        <div class="card-body">
          <form class="row gy-2 gx-2 align-items-end">
            <div class="col-12 col-md-5">
              <label class="form-label">Student</label>
              <select class="form-select" name="student_id" required disabled>
                <option value="">-- Select Student --</option>
                <?php foreach ($students as $i => $s): ?>
                  <option value="<?= $i ?>"><?= htmlspecialchars($s) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-12 col-md-5">
              <label class="form-label">Class</label>
              <select class="form-select" name="class_id" required disabled>
                <option value="">-- Select Class --</option>
                <?php foreach ($classes as $i => $c): ?>
                  <option value="<?= $i ?>"><?= htmlspecialchars($c) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-12 col-md-2 text-md-end">
              <button type="button" class="btn btn-success w-100" disabled>Assign</button>
            </div>
          </form>
          <div class="alert alert-info mt-3 mb-0">-</div>
        </div>
      </div>
      <div class="card mt-3">
        <div class="card-header">Assignments</div>
        <div class="card-body p-0">
          <ul class="list-group list-group-flush">
            <li class="list-group-item text-muted">-</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
