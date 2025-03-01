<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Flipable Modal with Image Trigger - Centered</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Ensure modal dialog is centered vertically and horizontally */
    .modal-dialog-centered {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
    }
    /* Flip container perspective */
    .flip-container {
      perspective: 1000px;
    }
    /* Modal inner container for 3D rotation */
    .modal-inner {
      position: relative;
      width: 100%;
      transform-style: preserve-3d;
      transition: transform 0.6s;
    }
    /* Both sides share the same size and are absolutely positioned */
    .modal-front, 
    .modal-back {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      backface-visibility: hidden;
    }
    /* Back side rotated */
    .modal-back {
      transform: rotateY(180deg);
    }
    /* When the container has the 'flipped' class, rotate inner content */
    .flip-container.flipped .modal-inner {
      transform: rotateY(180deg);
    }
    /* Style for flip trigger images */
    .flip-trigger-image {
      width: 50px;
      height: 50px;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <!-- Image to trigger the modal -->
  <div class="container my-5 text-center">
    <!-- Replace the src with your desired image -->
    <img src="https://via.placeholder.com/150" alt="Open Modal" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#flipModal">
  </div>

  <!-- Flippable Modal -->
  <div class="modal fade" id="flipModal" tabindex="-1" aria-labelledby="flipModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content flip-container">
        <div class="modal-inner">
          <!-- Front Side -->
          <div class="modal-front">
            <div class="modal-header">
              <h5 class="modal-title" id="flipModalLabel">Front Side</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
              <p>This is the front side of the modal.</p>
              <p>Click the image below to flip to the back side.</p>
              <!-- Replace with your desired image for flipping -->
              <img src="https://via.placeholder.com/50" class="flip-trigger-image" alt="Flip to Back">
            </div>
          </div>
          <!-- Back Side -->
          <div class="modal-back">
            <div class="modal-header">
              <h5 class="modal-title">Back Side</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
              <p>This is the back side of the modal.</p>
              <p>Click the image below to flip back to the front side.</p>
              <!-- Replace with your desired image for flipping -->
              <img src="https://via.placeholder.com/50" class="flip-trigger-image" alt="Flip to Front">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Toggle the flipped class on the modal content when clicking the flip trigger image
    document.querySelectorAll('.flip-trigger-image').forEach(image => {
      image.addEventListener('click', function(e) {
        // Prevent any unintended propagation
        e.stopPropagation();
        document.querySelector('.flip-container').classList.toggle('flipped');
      });
    });
  </script>
</body>
</html>
