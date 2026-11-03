<?php
/**
 * CYBER SHIELD - Contact & Security Inquiry Form
 * File: contact.php
 * BCA Final Year Project
 */

declare(strict_types=1);

$pageTitle = 'Contact Security Team';
$activeNav = 'contact';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$user = getCurrentUser();

// Handle form submission via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!validateCSRFToken($token)) {
        setFlashMessage('danger', 'Security validation token expired. Please try again.');
    } else {
        $name = sanitizeInput($_POST['name'] ?? '');
        $email = sanitizeEmail($_POST['email'] ?? '');
        $subject = sanitizeInput($_POST['subject'] ?? '');
        $message = sanitizeInput($_POST['message'] ?? '');

        if (empty($name) || !$email || empty($subject) || empty($message)) {
            setFlashMessage('danger', 'All form fields are mandatory.');
        } else {
            $db = getDBConnection();
            try {
                $stmt = $db->prepare("
                    INSERT INTO feedback (user_id, name, email, subject, message, status, created_at)
                    VALUES (?, ?, ?, ?, ?, 'unread', NOW())
                ");
                $stmt->execute([$user['id'] ?? null, $name, $email, $subject, $message]);
                setFlashMessage('success', 'Your inquiry has been successfully sent to the Cyber Shield administrator team.');
                header('Location: ' . BASE_URL . '/contact.php');
                exit;
            } catch (PDOException $e) {
                error_log("Contact submit error: " . $e->getMessage());
                setFlashMessage('danger', 'Database error while saving inquiry. Please try again later.');
            }
        }
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-5">
                <div class="hero-badge">
                    <i class="fa-solid fa-headset text-cyan"></i>
                    <span>FEEDBACK & VIVA SUPPORT</span>
                </div>
                <h1 class="text-white fw-bold">Contact Security Analysts</h1>
                <p class="text-secondary max-w-xl mx-auto">
                    Have a question regarding false positives, project evaluation viva, or security architecture? Submit an inquiry below.
                </p>
            </div>

            <div class="cyber-card p-4 p-md-5">
                <form action="contact.php" method="POST">
                    <?= csrfField() ?>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-secondary small fw-semibold">Your Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary border-opacity-50 text-cyan">
                                    <i class="fa-solid fa-user"></i>
                                </span>
                                <input 
                                    type="text" 
                                    name="name" 
                                    class="form-control bg-dark text-white border-secondary border-opacity-50" 
                                    placeholder="e.g. Alex Morgan"
                                    value="<?= htmlspecialchars($user['name'] ?? $_POST['name'] ?? '') ?>" 
                                    required
                                >
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-secondary small fw-semibold">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary border-opacity-50 text-cyan">
                                    <i class="fa-solid fa-envelope"></i>
                                </span>
                                <input 
                                    type="email" 
                                    name="email" 
                                    class="form-control bg-dark text-white border-secondary border-opacity-50" 
                                    placeholder="name@example.com"
                                    value="<?= htmlspecialchars($user['email'] ?? $_POST['email'] ?? '') ?>" 
                                    required
                                >
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-semibold">Subject</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary border-opacity-50 text-cyan">
                                <i class="fa-solid fa-tag"></i>
                            </span>
                            <input 
                                type="text" 
                                name="subject" 
                                class="form-control bg-dark text-white border-secondary border-opacity-50" 
                                placeholder="e.g. False Positive Review / BCA Viva Demo Request"
                                value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>" 
                                required
                            >
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-secondary small fw-semibold">Detailed Message</label>
                        <textarea 
                            name="message" 
                            rows="5" 
                            class="form-control bg-dark text-white border-secondary border-opacity-50" 
                            placeholder="Describe your query, scan ID, or feedback..." 
                            required
                        ><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-secondary small">
                            <i class="fa-solid fa-shield-halved text-cyan me-1"></i> Submissions saved to MySQL `feedback`
                        </span>
                        <button type="submit" class="btn btn-cyan px-4">
                            <i class="fa-solid fa-paper-plane me-1"></i> Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
