<?php
session_start();
require_once 'Student.php';
require_once 'ClassModel.php';

if (!isset($_SESSION['students'])) $_SESSION['students'] = [];
if (!isset($_SESSION['classes'])) $_SESSION['classes'] = [];
if (!isset($_SESSION['assignments'])) $_SESSION['assignments'] = [];

// Handle add student
if (isset($_POST['add_student']) && !empty($_POST['student_name'])) {
  Student::add($_SESSION['students'], $_POST['student_name']);
  header('Location: index.php'); exit;
}
// Handle update student
if (isset($_POST['update_student']) && isset($_POST['student_index']) && isset($_POST['student_name'])) {
  Student::update($_SESSION['students'], (int)$_POST['student_index'], $_POST['student_name']);
  header('Location: index.php'); exit;
}
// Handle delete student
if (isset($_POST['delete_student']) && isset($_POST['student_index'])) {
  Student::delete($_SESSION['students'], (int)$_POST['student_index']);
  header('Location: index.php'); exit;
}
// Handle add class
if (isset($_POST['add_class']) && !empty($_POST['class_name'])) {
  ClassModel::add($_SESSION['classes'], $_POST['class_name']);
  header('Location: index.php'); exit;
}
// Handle update class
if (isset($_POST['update_class']) && isset($_POST['class_index']) && isset($_POST['class_name'])) {
  ClassModel::update($_SESSION['classes'], (int)$_POST['class_index'], $_POST['class_name']);
  header('Location: index.php'); exit;
}
// Handle delete class
if (isset($_POST['delete_class']) && isset($_POST['class_index'])) {
  ClassModel::delete($_SESSION['classes'], (int)$_POST['class_index']);
  header('Location: index.php'); exit;
}

// Handle assign student to class (Create)
if (isset($_POST['assign']) && isset($_POST['student_id']) && isset($_POST['class_id'])) {
  $sid = (int)$_POST['student_id'];
  $cid = (int)$_POST['class_id'];

  // Make sure indexes exist
  if (isset($_SESSION['students'][$sid]) && isset($_SESSION['classes'][$cid])) {
    // Optional: avoid duplicate assignment
    $exists = false;
    foreach ($_SESSION['assignments'] as $a) {
      if ($a['student_id'] === $sid && $a['class_id'] === $cid) { $exists = true; break; }
    }
    if (!$exists) {
      $_SESSION['assignments'][] = ['student_id' => $sid, 'class_id' => $cid];
    }
  }
  header('Location: index.php'); exit;
}

// Handle delete assignment
if (isset($_POST['delete_assignment']) && isset($_POST['assignment_index'])) {
  $idx = (int)$_POST['assignment_index'];
  if (isset($_SESSION['assignments'][$idx])) {
    array_splice($_SESSION['assignments'], $idx, 1);
  }
  header('Location: index.php'); exit;
}

// Handle edit assignment (show modal)
$editAssignment = null;
if (isset($_GET['edit_assignment']) && isset($_GET['assignment_index'])) {
  $idx = (int)$_GET['assignment_index'];
  if (isset($_SESSION['assignments'][$idx])) {
    $editAssignment = [
      'index' => $idx,
      'student_id' => $_SESSION['assignments'][$idx]['student_id'],
      'class_id' => $_SESSION['assignments'][$idx]['class_id']
    ];
  }
}

// Handle update assignment
if (isset($_POST['update_assignment']) && isset($_POST['assignment_index']) && isset($_POST['student_id']) && isset($_POST['class_id'])) {
  $idx = (int)$_POST['assignment_index'];
  $sid = (int)$_POST['student_id'];
  $cid = (int)$_POST['class_id'];
  if (isset($_SESSION['assignments'][$idx])) {
    $_SESSION['assignments'][$idx] = ['student_id' => $sid, 'class_id' => $cid];
  }
  header('Location: index.php'); exit;
}

$students = Student::getAll($_SESSION['students']);
$classes = ClassModel::getAll($_SESSION['classes']);
$assignments = $_SESSION['assignments'];
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
                    <form method="POST">
                      <div class="modal-header">
                        <h5 class="modal-title" id="editStudentLabel<?= $i ?>">Edit Student</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <div class="mb-3">
                          <label class="form-label">Student Name</label>
                          <input type="text" class="form-control" name="student_name" value="<?= htmlspecialchars($s) ?>" required>
                          <input type="hidden" name="student_index" value="<?= $i ?>">
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="update_student">Save changes</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
              <!-- Delete Student Modal -->
              <div class="modal fade" id="deleteStudentModal<?= $i ?>" tabindex="-1" aria-labelledby="deleteStudentLabel<?= $i ?>" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <form method="POST">
                      <div class="modal-header">
                        <h5 class="modal-title" id="deleteStudentLabel<?= $i ?>">Delete Student</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        Are you sure you want to delete <strong><?= htmlspecialchars($s) ?></strong>?<br>
                        <input type="hidden" name="student_index" value="<?= $i ?>">
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger" name="delete_student">Delete</button>
                      </div>
                    </form>
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
                    <form method="POST">
                      <div class="modal-header">
                        <h5 class="modal-title" id="editClassLabel<?= $i ?>">Edit Class</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <div class="mb-3">
                          <label class="form-label">Class Name</label>
                          <input type="text" class="form-control" name="class_name" value="<?= htmlspecialchars($c) ?>" required>
                          <input type="hidden" name="class_index" value="<?= $i ?>">
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="update_class">Save changes</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
              <!-- Delete Class Modal -->
              <div class="modal fade" id="deleteClassModal<?= $i ?>" tabindex="-1" aria-labelledby="deleteClassLabel<?= $i ?>" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <form method="POST">
                      <div class="modal-header">
                        <h5 class="modal-title" id="deleteClassLabel<?= $i ?>">Delete Class</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        Are you sure you want to delete <strong><?= htmlspecialchars($c) ?></strong>?<br>
                        <input type="hidden" name="class_index" value="<?= $i ?>">
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger" name="delete_class">Delete</button>
                      </div>
                    </form>
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

  <!-- Assign Student to Class (works with sessions, no SQL) -->
  <div class="row g-4 mt-4">
    <div class="col-md-8 mx-auto">
      <div class="card">
        <div class="card-header">Assign Student to Class</div>
        <div class="card-body">
          <form method="POST" class="row gy-2 gx-2 align-items-end">
            <div class="col-12 col-md-5">
              <label class="form-label">Student</label>
              <select class="form-select" name="student_id" required>
                <option value="">-- Select Student --</option>
                <?php foreach ($students as $i => $s): ?>
                  <option value="<?= $i ?>"><?= htmlspecialchars($s) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-12 col-md-5">
              <label class="form-label">Class</label>
              <select class="form-select" name="class_id" required>
                <option value="">-- Select Class --</option>
                <?php foreach ($classes as $i => $c): ?>
                  <option value="<?= $i ?>"><?= htmlspecialchars($c) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-12 col-md-2 text-md-end">
              <button type="submit" class="btn btn-success w-100" name="assign">Assign</button>
            </div>
          </form>
        </div>
      </div>
      <div class="card mt-3">
        <div class="card-header">Assignments</div>
        <div class="card-body p-0">
          <ul class="list-group list-group-flush">
            <?php
            $shown = false;
            foreach ($assignments as $i => $a):
              $sid = $a['student_id'];
              $cid = $a['class_id'];
              // Skip invalid pairs if indexes no longer exist
              if (!isset($students[$sid]) || !isset($classes[$cid])) continue;
              $shown = true;
            ?>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <span><?= htmlspecialchars($students[$sid]) ?> ➜ <?= htmlspecialchars($classes[$cid]) ?></span>
                <span>
                  <!-- Edit button (link triggers modal via GET) -->
                  <a href="?edit_assignment=1&assignment_index=<?= $i ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                  <!-- Delete button (form POST) -->
                  <form method="POST" style="display:inline;">
                    <input type="hidden" name="assignment_index" value="<?= $i ?>">
                    <button type="submit" name="delete_assignment" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this assignment?')">Delete</button>
                  </form>
                </span>
              </li>
            <?php endforeach; ?>
            <?php if (!$shown): ?>
              <li class="list-group-item text-muted">No assignments yet.</li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
      <!-- Edit Assignment Modal -->
      <?php if ($editAssignment): ?>
        <div class="modal show" tabindex="-1" style="display:block; background:rgba(0,0,0,0.5);">
          <div class="modal-dialog">
            <div class="modal-content">
              <form method="POST">
                <div class="modal-header">
                  <h5 class="modal-title">Edit Assignment</h5>
                  <a href="index.php" class="btn-close"></a>
                </div>
                <div class="modal-body">
                  <input type="hidden" name="assignment_index" value="<?= $editAssignment['index'] ?>">
                  <div class="mb-3">
                    <label class="form-label">Student</label>
                    <select class="form-select" name="student_id" required>
                      <?php foreach ($students as $i => $s): ?>
                        <option value="<?= $i ?>" <?= $i == $editAssignment['student_id'] ? 'selected' : '' ?>>
                          <?= htmlspecialchars($s) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Class</label>
                    <select class="form-select" name="class_id" required>
                      <?php foreach ($classes as $i => $c): ?>
                        <option value="<?= $i ?>" <?= $i == $editAssignment['class_id'] ? 'selected' : '' ?>>
                          <?= htmlspecialchars($c) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
                <div class="modal-footer">
                  <a href="index.php" class="btn btn-secondary">Cancel</a>
                  <button type="submit" class="btn btn-primary" name="update_assignment">Save changes</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <script>
          // Auto-close modal on background click
          document.querySelector('.modal').addEventListener('click', function(e) {
            if (e.target === this) window.location = 'index.php';
          });
        </script>
      <?php endif; ?>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>