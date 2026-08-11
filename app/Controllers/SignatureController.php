<?php

class SignatureController extends Controller
{
    // GET /signature — list + builder form (with live preview)
    public function index()
    {
        $this->requireLogin();
        $sigModel = $this->model('Signature');

        $this->view('signature/index', [
            'title' => 'Email Signatures',
            'sigs'  => $sigModel->getByUser(Auth::id()),
        ]);
    }

    // POST /signature/save — create or update (edit_id present = update)
    public function save()
    {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/signature');
        }

        $userId = Auth::id();
        $sigModel = $this->model('Signature');

        $imageBase64 = '';
        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $data = file_get_contents($_FILES['image']['tmp_name']);
            if (strlen($data) > 400000) {
                $_SESSION['signature_error'] = 'Image too large. Please use an image under 400 KB.';
                $this->redirect('/signature');
            }
            $mime = mime_content_type($_FILES['image']['tmp_name']) ?: 'image/png';
            $imageBase64 = 'data:' . $mime . ';base64,' . base64_encode($data);
        }

        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            $_SESSION['signature_error'] = 'Name is required.';
            $this->redirect('/signature');
        }

        $data = [
            'name'          => $name,
            'title'         => trim($_POST['title'] ?? ''),
            'email'         => trim($_POST['email'] ?? ''),
            'phone'         => trim($_POST['phone'] ?? ''),
            'linkedin'      => trim($_POST['linkedin'] ?? ''),
            'github'        => trim($_POST['github'] ?? ''),
            'portfolio'     => trim($_POST['portfolio'] ?? ''),
            'custom_text'   => trim($_POST['custom_text'] ?? ''),
            'image_shape'   => $_POST['image_shape'] ?? 'circle',
            'image_size'    => (int) ($_POST['image_size'] ?? 80),
            'layout'        => $_POST['layout'] ?? 'horizontal',
            'accent_color'  => $_POST['accent_color'] ?? '#3b82f6',
            'show_icons'    => isset($_POST['show_icons']),
            'font_family'   => $_POST['font_family'] ?? 'Arial, Helvetica, sans-serif',
            'links_columns' => (int) ($_POST['links_columns'] ?? 1),
            'image_base64'  => $imageBase64 ?: null,
        ];

        $editId = $_POST['edit_id'] ?? null;
        if ($editId) {
            $sigModel->update((int) $editId, $userId, $data);
            $_SESSION['signature_success'] = 'Signature updated!';
        } else {
            $sigModel->create($userId, $data);
            $_SESSION['signature_success'] = 'Signature created!';
        }

        $this->redirect('/signature');
    }

    // POST /signature/delete/{id}
    public function delete($id = null)
    {
        $this->requireLogin();
        if ($id) {
            $sigModel = $this->model('Signature');
            $sigModel->delete((int) $id, Auth::id());
            $_SESSION['signature_success'] = 'Signature deleted.';
        }
        $this->redirect('/signature');
    }
}
