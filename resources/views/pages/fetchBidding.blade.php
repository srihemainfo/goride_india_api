<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Your Driver | GoRide Premium</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-firestore.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --glass-bg: rgba(255, 255, 255, 0.95);
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            --accent: #f59e0b;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --bg-soft: #f8fafc;
            --white: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg-soft);
            color: var(--text-main);
            line-height: 1.6;
            padding-bottom: 40px;
        }

        .bg-blob {
            position: fixed;
            width: 300px;
            height: 300px;
            background: var(--primary-gradient);
            filter: blur(100px);
            opacity: 0.1;
            z-index: -1;
            top: -100px;
            right: -100px;
        }

        .container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
        }

        .app-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
            padding-top: 10px;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 12px;
            background: #fff;
            margin-bottom: 8px;
            border: 1px solid #eef2f7;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
            transition: all 0.2s ease;
        }

        .btn-back {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            cursor: pointer;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .page-title {
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: -0.5px;
        }

        .btn-cancel {
            background: #fee2e2;
            color: #ef4444;
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }

        .summary-card {
            background: var(--white);
            border-radius: 24px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.8);
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .job-tag {
            display: inline-block;
            background: #eff6ff;
            color: #2563eb;
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .route-visual {
            position: relative;
            padding-left: 25px;
        }

        /*.route-visual::before {*/
        /*    content: '';*/
        /*    position: absolute;*/
        /*    left: 4px;*/
        /*    top: 10px;*/
        /*    bottom: 10px;*/
        /*    width: 2px;*/
        /*    background: #e2e8f0;*/
        /*    border-style: dashed;*/
        /*}*/

        .route-step {
            position: relative;
            margin-bottom: 15px;
        }

        .route-step i {
            position: absolute;
            left: -26px;
            background: var(--white);
            font-size: 12px;
        }

        .route-step .dot-start {
            color: #22c55e;
        }

        .route-step .dot-end {
            color: #ef4444;
        }

        .route-step p {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-main);
        }

        .route-step span {
            display: block;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            font-size: 13px;
        }

        .trip-meta {
            display: flex;
            justify-content: space-between;
            padding-top: 15px;
            border-top: 1px solid #f1f5f9;
        }

        .meta-item {
            text-align: center;
        }

        .meta-item .val {
            display: block;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .meta-item .lbl {
            font-size: 11px;
            color: var(--text-muted);
            text-transform: uppercase;
            font-weight: 600;
        }

        .section-label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 15px;
        }

        .section-label h3 {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }

        .section-label i {
            font-size: 16px;
            color: white;
            background: #f6ba02;
            transform: scale(1.05);
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .driver-card {
            display: grid;
            grid-template-columns: 42px 1fr auto;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 12px;
            background: #fff;
            margin-bottom: 8px;
            border: 1px solid #eef2f7;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
            transition: all 0.2s ease;
        }

        .driver-card:hover {
            border-color: #f5a3096e;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
        }

        .best-deal-badge {
            position: absolute;
            top: -10px;
            right: 20px;
            background: var(--accent);
            color: white;
            font-size: 0.7rem;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 20px;
            box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3);
        }

        .driver-info-row {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .driver-avatar {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            object-fit: cover;
            background: #f1f5f9;
        }

        .driver-center {
            min-width: 0;
        }

        /*.driver-details h4 {*/
        /*    font-size: 0.95rem;*/
        /*    font-weight: 700;*/
        /*}*/

        .driver-name {
            font-size: 13px;
            font-weight: 600;
            color: #111827;
        }

        .driver-verified {
            color: #22c55e;
            margin-left: 4px;
            font-size: 11px;
        }

        .driver-meta {
            font-size: 11px;
            color: #64748b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .driver-right {
            text-align: right;
        }

        .rating {
            font-size: 0.75rem;
            color: var(--accent);
            font-weight: 600;
        }

        .vehicle-preview {
            background: #f8fafc;
            border-radius: 14px;
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .vehicle-img {
            height: 50px;
            width: auto;
            object-fit: contain;
        }

        .vehicle-meta {
            text-align: right;
        }

        .vehicle-name {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .vehicle-specs {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .action-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 5px;
        }

        .price-tag {
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .price-tag small {
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--text-muted);
        }

        .btn-accept {
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }

        .btn-accept:active {
            transform: scale(0.95);
        }

        .price {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
        }

        .accept-btn {
            margin-top: 5px;
            font-size: 13px;
            padding: 5px 12px;
            border-radius: 8px;
            background: linear-gradient(135deg, #f6ba02, #f59e0b);
            color: black;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: 0.2s;
            border: none;
            cursor: pointer;
        }

        .best-deal {
            font-size: 12px;
            background: #22c55e;
            color: white;
            padding: 1px 8px;
            border-radius: 6px;
            display: inline-block;
            margin-bottom: 2px;
        }

        .vehicle-preview,
        .vehicle-img,
        .vehicle-meta,
        .action-row {
            display: none !important;
        }

        .more-info-btn {
            font-size: 13px;
            color: #090054;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            cursor: pointer;
        }


        .vehicle-details {
            margin-top: 8px;
            padding: 8px;
            border-radius: 8px;
            background: #f8fafc;
            display: none;
            font-size: 11px;
        }

        .vehicle-details img {
            width: 100%;
            max-height: 120px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 6px;
        }

        .searching-box {
            text-align: center;
            padding: 40px 20px;
            padding: 13px 21px;
            border-radius: 12px;
            background: #fff;
            margin-bottom: 8px;
            border: 1px solid #eef2f7;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
            transition: all 0.2s ease;
        }

        .pulse-loader {
            position: relative;
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
        }

        .pulse-loader div {
            position: absolute;
            width: 100%;
            height: 100%;
            background: var(--primary-gradient);
            border-radius: 50%;
            opacity: 0.6;
            animation: pulse-ring 2s infinite ease-in-out;
        }

        .pulse-loader div:nth-child(2) {
            animation-delay: 0.5s;
        }

        /* ============================================
   1. VEHICLE MODAL (More Info)
   ============================================ */
#vehicleModal {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.45);
    display: none;
    z-index: 999;
    align-items: center;
    justify-content: center;
}

#vehicleModal .vehicle-modal-sheet {
    position: relative;
    width: 90%;
    max-width: 460px;
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    max-height: 90vh;
    overflow-y: auto;
    animation: scaleIn 0.25s ease;
}

/* Close button floats above image */
#vehicleModal .vehicle-close-btn {
    position: absolute;
    right: 12px;
    top: 12px;
    width: 32px;
    height: 32px;
    background: rgba(255,255,255,0.9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 700;
    color: #374151;
    cursor: pointer;
    z-index: 10;
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

#vehicleModal .vehicle-close-btn:hover {
    background: #fee2e2;
    color: #ef4444;
}

/* Image fills top of modal, no gap */
#vehicleModal .main-vehicle-img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    display: block;
    border-radius: 0;
    margin: 0;
}

/* Content below image gets padding */
#vehicleModal .vehicle-modal-body {
    padding: 14px 16px 20px;
}

/* On mobile — slide up from bottom */
@media (max-width: 600px) {
    #vehicleModal {
        align-items: center;
        justify-content: center;
    }
    #vehicleModal .vehicle-modal-sheet {
        width: 100%;
        max-width: 100%;
        /*border-radius: 20px 20px 0 0;*/
        animation: slideUp 0.3s ease;
    }
}

@media (max-width: 768px) {
    .trip-meta{
            flex-direction: column;
    }
    .meta-item{
            display: flex;
    justify-content: space-between;
    }
}
/* ============================================
   2. CANCEL MODAL
   ============================================ */
#cancelModal {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.45);
    display: none;
    z-index: 999;
    align-items: flex-end;
    justify-content: center;
}

#cancelModal .cancel-modal-sheet {
    position: relative;
    width: 100%;
    max-width: 500px;
    background: #fff;
    border-radius: 20px 20px 0 0;
    padding: 20px;
    animation: slideUp 0.3s ease;
}

#cancelModal .cancel-handle {
    width: 40px;
    height: 4px;
    background: #d1d5db;
    border-radius: 10px;
    margin: 0 auto 15px;
}

/* On desktop — show as centered modal */
@media (min-width: 601px) {
    #cancelModal {
        align-items: center;
    }
    #cancelModal .cancel-modal-sheet {
        border-radius: 16px;
        width: 400px;
        animation: scaleIn 0.25s ease;
    }
}


/* ============================================
   3. PAYMENT DRAWER (Side sheet)
   ============================================ */
#paymentDrawer {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.4);
    display: none;
    z-index: 1000;
}

#paymentDrawer .payment-sheet {
    position: absolute;
    top: 0;
    right: 0;
    width: 100%;
    max-width: 420px;   /* ← side drawer on desktop */
    height: 100%;
    background: #fff;
    animation: slideRight 0.3s ease;
    overflow-y: auto;
}

/* On mobile — full width */
@media (max-width: 600px) {
    #paymentDrawer .payment-sheet {
        max-width: 100%;
    }
}

#paymentDrawer .payment-sheet-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 16px;
    border-bottom: 1px solid #f1f5f9;
    background: #fff;
    position: sticky;
    top: 0;
    z-index: 10;
}

#paymentDrawer .payment-sheet-header button {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    border: none;
    background: #f8fafc;
    color: #111827;
    font-size: 16px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: 0.2s;
}

#paymentDrawer .payment-sheet-header button:hover {
    background: #f6ba02;
    color: #000;
}

#paymentDrawer .payment-sheet-header h3 {
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    color: #111827;
}
        .vehicle-gallery {
            display: flex;
            gap: 6px;
            overflow-x: auto;
            margin-bottom: 12px;
        }

        .vehicle-gallery img {
            width: 65px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
            cursor: pointer;
            border: 2px solid transparent;
        }

        .vehicle-gallery img:hover {
            border-color: #6366f1;
        }

        .vehicle-title {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .vehicle-badge {
            display: inline-block;
            font-size: 10px;
            padding: 3px 8px;
            border-radius: 6px;
            background: #dcfce7;
            color: #16a34a;
            margin-bottom: 10px;
        }

        .vehicle-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .vehicle-item {
            background: #f8fafc;
            padding: 8px;
            border-radius: 8px;
            font-size: 12px;
        }

        .vehicle-item b {
            display: block;
            font-size: 11px;
            color: #64748b;
        }

    

        .payment-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            display: none;
            z-index: 1000;
        }

  


        #paymentContent {
            padding: 20px;
            text-align: center;

            font-size: 13px;
        }

        .payment-container {
            padding: 16px;
        }

        .payment-card {
            background: #fff;
            border-radius: 16px;
            padding: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }

        .section-title {
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 12px;
        }

        .row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .row b {
            font-weight: 600;
        }

        .row.green {
            color: #16a34a;
        }

        .divider {
            height: 1px;
            background: #f1f5f9;
            margin: 10px 0;
        }

        .row.total {
            font-size: 15px;
            font-weight: 700;
        }

        .no-charge {
            margin-top: 8px;
            font-size: 11px;
            color: #16a34a;
            background: #ecfdf5;
            padding: 4px 8px;
            border-radius: 6px;
            display: inline-block;
        }

        .confirm-btn {
            width: 100%;
            margin-top: 15px;
            padding: 14px;
            border-radius: 12px;
            border: none;
            font-weight: 700;
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            color: #000;
            font-size: 14px;
            box-shadow: 0 8px 20px rgba(251, 191, 36, 0.4);
            cursor:pointer;
        }

        .success-container {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(180deg, #f8fafc, #eef2ff);
        }

        .success-card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            max-width: 350px;
        }

        .success-icon {
            width: 60px;
            height: 60px;
            background: #22c55e;
            color: white;
            font-size: 28px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            margin: 0 auto 15px;
        }

        .success-card h2 {
            margin-bottom: 5px;
        }

        .success-card p {
            color: #64748b;
            font-size: 14px;
        }

        .success-details {
            margin: 15px 0;
            font-size: 13px;
        }

        .home-btn {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: none;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: white;
            font-weight: 600;
        }

        .logo-box {
            display: flex;
            align-items: center;
        }

        .logo-box img {
            height: 30px;
            opacity: 0.9;
        }

        @keyframes slideUp {
            from {
                transform: translateY(100%);
            }

            to {
                transform: translateY(0);
            }
        }

        @keyframes slideRight {
            from {
                transform: translateX(100%);
            }

            to {
                transform: translateX(0);
            }
        }

        @keyframes slideUp {
            from {
                transform: translateY(100%);
            }

            to {
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                transform: scale(0.95);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes pulse-ring {
            0% {
                transform: scale(0.5);
                opacity: 0;
            }

            50% {
                opacity: 0.5;
            }

            100% {
                transform: scale(1.5);
                opacity: 0;
            }
        }

        @media (max-width: 400px) {
            .container {
                padding: 15px;
            }

            .price-tag {
                font-size: 1.1rem;
            }
        }


        @media screen and (max-width: 991px) {
            .v-middle p {
                font-size: 12px !important;
            }
        }

        /* ANIMATIONS */
        /*@keyframes slideUp {*/
        /*    from {*/
        /*        transform: translate(-50%, 100%);*/
        /*    }*/

        /*    to {*/
        /*        transform: translate(-50%, 0);*/
        /*    }*/
        /*}*/

        @keyframes scaleIn {
            from {
                transform: scale(0.9);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .search-animation {
            position: relative;
            width: 140px;
            height: 140px;
            margin: 0 auto 20px;
        }

        .glow {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border-radius: 50%;
            background: rgba(246, 186, 2, 0.25);
        }

        .g1 {
            width: 80px;
            height: 80px;
            animation: glowPulse 2s infinite;
        }

        .g2 {
            width: 110px;
            height: 110px;
            animation: glowPulse 2s infinite 0.4s;
        }

        .g3 {
            width: 140px;
            height: 140px;
            animation: glowPulse 2s infinite 0.8s;
        }

        @keyframes glowPulse {
            0% {
                opacity: 0.6;
                transform: translate(-50%, -50%) scale(0.8);
            }

            70% {
                opacity: 0;
                transform: translate(-50%, -50%) scale(1.3);
            }

            100% {
                opacity: 0;
            }
        }

        .search-core {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 55px;
            height: 55px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f6ba02, #f59e0b);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 20px;
            box-shadow: 0 8px 20px rgba(246, 186, 2, 0.4);
            z-index: 2;
        }

        .orbit {
            position: absolute;
            z-index: 3;
        }

        .orbit i {
            font-size: 15px;
            color: #f59e0b;
        }

        @keyframes floatY {
            0% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-12px);
            }

            100% {
                transform: translateY(0);
            }
        }

        .orbit1 {
            top: 5%;
            left: 50%;
            transform: translateX(-50%);
            animation: floatY 2.5s ease-in-out infinite;
        }

        .orbit2 {
            bottom: 5%;
            left: 5%;
            animation: floatY 3s ease-in-out infinite;
        }

        .orbit3 {
            bottom: 5%;
            right: 5%;
            animation: floatY 2.8s ease-in-out infinite;
        }

        .icon-pickup {
            color: #22c55e;
        }

        .icon-destination {
            color: #ef4444;
        }

        .icon-distance {
            color: #3b82f6;
        }

        .icon-duration {
            color: #a855f7;
        }

        .icon-trip {
            color: #f97316;
        }

        .icon-cab {
            color: #f6ba02;
        }

        .meta-item .lbl i {
            margin-right: 5px;
            font-size: 12px;
        }

        .icon-pickup {
            color: #22c55e;
            font-size: 18px;
        }

        .icon-destination {
            color: #ef4444;
            font-size: 14px;
        }
    </style>
    
</head>

<body>

    <div class="bg-blob"></div>

    <div class="container">
        <header class="app-header">
            <div class="logo-box" onclick="goHome()" style="cursor:pointer;">
                <img src="{{ asset('goride/img/logo-light.png') }}" alt="GoRide">
            </div>

            <div class="page-title">Select Driver</div>

            <button class="btn-cancel" onclick="openCancelModal()">Cancel</button>
        </header>

        <div class="summary-card">
            <span class="job-tag">ID: {{ $job_no }}</span>

            <div class="route-visual">
                <div class="route-step">
                    <i class="bi bi-geo-alt-fill icon-pickup"></i>
                    <span>Pickup</span>
                    <p>{{$from_place}}</p>
                </div>

                <div class="route-step">
                    <i class="bi bi-geo-alt-fill icon-destination"></i>
                    <span>Destination</span>
                    <p>{{$to_place}}</p>
                </div>
            </div>

            <div class="trip-meta">
                <div class="meta-item">
                    <span class="lbl">
                        <i class="bi bi-signpost-2-fill icon-distance"></i> Distance
                    </span>
                    <span class="val">{{$distance}} km</span>
                </div>

                <div class="meta-item">
                    <span class="lbl">
                        <i class="bi bi-clock-fill icon-duration"></i> Duration
                    </span>
                    <span class="val">~{{$day}}</span>
                </div>

                <div class="meta-item">
                    <span class="lbl">
                        <i class="bi bi-arrow-repeat icon-trip"></i> Trip
                    </span>
                    <span class="val">{{ ucfirst($job_type) }}</span>
                </div>

                <div class="meta-item">
                    <span class="lbl">
                        <i class="bi bi-car-front-fill icon-cab"></i> Cab
                    </span>
                    <span class="val">{{$cab_type}}</span>
                </div>
            </div>
        </div>

        <div class="section-label">
            <h3 id="bidCount">Finding bids...</h3>
            <!--<i class="bi bi-sliders"></i>-->
        </div>

        <div id="driverList"></div>

        <div id="emptyState" class="searching-box">
            <div class="search-animation">

                <span class="glow g1"></span>
                <span class="glow g2"></span>
                <span class="glow g3"></span>

                <div class="search-core">
                    <i class="bi bi-search"></i>
                </div>

                <div class="orbit orbit1">
                    <i class="bi bi-car-front-fill"></i>
                </div>

                <div class="orbit orbit2">
                    <i class="bi bi-car-front-fill"></i>
                </div>

                <div class="orbit orbit3">
                    <i class="bi bi-car-front-fill"></i>
                </div>

            </div>
            <h4 style="margin-bottom: 8px; font-weight: 700;">Searching nearby drivers...</h4>
            <p style="font-size: 15px;">Matching your request with premium fleet owners and independent drivers.</p>

        </div>

        <div id="vehicleModal">

            <div class="vehicle-modal-sheet">

                <button class="vehicle-close-btn" onclick="closeVehicleModal()">✕</button>

                <div id="modalContent"></div>
            </div>
        </div>

        <!--<div id="cancelModal">-->

        <!--    <div class="cancel-modal-sheet">-->

        <!--         <div class="cancel-handle"></div>-->

        <!--        <h4 style="margin-bottom:10px;">Cancel Ride</h4>-->

        <!--        <textarea id="cancelReason" placeholder="Reason (optional)"-->
        <!--            style="width:100%;padding:8px;border-radius:8px;border:1px solid #e5e7eb;"></textarea>-->

        <!--        <div style="display:flex;gap:10px;margin-top:12px;">-->
        <!--            <button onclick="closeCancelModal()"-->
        <!--                style="flex:1;padding:10px;border-radius:8px;border:none;background:#e5e7eb;cursor:pointer;">-->
        <!--                Close-->
        <!--            </button>-->

        <!--            <button onclick="submitCancelJob()"-->
        <!--                style="flex:1;padding:10px;border-radius:8px;border:none;background:#ef4444;color:white;cursor:pointer;">-->
        <!--                Confirm Cancel-->
        <!--            </button>-->
        <!--        </div>-->

        <!--    </div>-->
        <!--</div>-->
        
        <div id="cancelModal" class="modal-overlay">
            <style>
                /* Backdrop blur/dim overlay to center everything */
                .modal-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100vw;
                    height: 100vh;
                    background: rgba(0, 0, 0, 0.5);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    z-index: 9999;
                    box-sizing: border-box;
                    padding: 20px;
                }
        
                /* Core Card Component */
                .cancel-modal-sheet {
                    width: 100%;
                    max-width: 400px;
                    background: #ffffff;
                    border-radius: 16px;
                    padding: 16px;
                    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                    display: flex;
                    flex-direction: column;
                    max-height: 85vh; /* Prevents modal from ever exceeding screen height */
                    box-sizing: border-box;
                }
        
                .cancel-handle {
                    width: 40px;
                    height: 4px;
                    background: #e5e7eb;
                    border-radius: 2px;
                    margin: 0 auto 12px auto;
                    display: none; /* Hidden on desktop, shown on mobile */
                }
        
                /* Crucial fix: This box handles internal scrolling gracefully */
                .reason-list {
                    margin-bottom: 12px;
                    border: 1px solid #e5e7eb;
                    border-radius: 8px;
                    overflow-y: auto; /* Triggers custom scrolling internally */
                    background: #fafafa;
                    flex: 1; 
                }
        
                .reason-item {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    padding: 12px;
                    border-bottom: 1px solid #f3f4f6;
                    cursor: pointer;
                    font-size: 14px;
                    transition: background 0.2s;
                    color: #374151;
                }
        
                .reason-item:last-of-type {
                    border-bottom: none;
                }
        
                .reason-item input[type="radio"] {
                    margin: 0;
                    width: 16px;
                    height: 16px;
                    accent-color: #ef4444; /* Match color palette */
                }
        
                .reason-item:hover {
                    background: #f9fafb;
                }
        
                /* Cleaner CSS-driven hidden state avoiding !important conflicts */
                .reason-item.hidden-reason {
                    display: none;
                }
        
                .toggle-btn {
                    width: 100%;
                    padding: 12px;
                    background: #ffffff;
                    border: none;
                    color: #2563eb;
                    font-size: 14px;
                    font-weight: 600;
                    cursor: pointer;
                    text-align: center;
                    border-top: 1px solid #e5e7eb;
                    position: sticky;
                    bottom: 0; /* Keeps show more button visible when scrolling down */
                }
        
                .toggle-btn:hover {
                    text-decoration: underline;
                }
        
                /* --- Responsive Viewport Adaptations --- */
                @media (max-width: 480px) {
                    .modal-overlay {
                        align-items: flex-end; /* Snaps modal to bottom of screen */
                        padding: 0;
                    }
        
                    .cancel-modal-sheet {
                        max-width: 100%;
                        max-height: 90vh; /* Takes up clean majority of screen space safely */
                        border-radius: 20px 20px 0 0;
                        padding: 16px 16px calc(16px + env(safe-area-inset-bottom)) 16px;
                    }
        
                    .cancel-handle {
                        display: block; /* Shows interactive drag handle mimic bar */
                    }
                }
            </style>
        
            <div class="cancel-modal-sheet">
                <div class="cancel-handle"></div>
                <h4 style="margin: 0 0 12px 0; font-size: 18px; color: #111827; font-weight: 700;">Cancel Ride</h4>
                
                <div class="reason-list">
                    <label class="reason-item">
                        <input type="radio" name="cancelReasonRadio" value="Driver is taking too long to arrive" checked>
                        <span>Driver is taking too long to arrive</span>
                    </label>
                    <label class="reason-item">
                        <input type="radio" name="cancelReasonRadio" value="Change of travel plan">
                        <span>Change of travel plan</span>
                    </label>
                    <label class="reason-item">
                        <input type="radio" name="cancelReasonRadio" value="Booking by mistake">
                        <span>Booking by mistake</span>
                    </label>
                    <label class="reason-item">
                        <input type="radio" name="cancelReasonRadio" value="Price is too high / fare issue">
                        <span>Price is too high / fare issue</span>
                    </label>
        
                    <label class="reason-item hidden-reason extra-reason">
                        <input type="radio" name="cancelReasonRadio" value="Driver not assigned / No driver available">
                        <span>Driver not assigned / No driver available</span>
                    </label>
                    <label class="reason-item hidden-reason extra-reason">
                        <input type="radio" name="cancelReasonRadio" value="Driver asked to cancel the trip">
                        <span>Driver asked to cancel the trip</span>
                    </label>
                    <label class="reason-item hidden-reason extra-reason">
                        <input type="radio" name="cancelReasonRadio" value="Driver not responding to calls">
                        <span>Driver not responding to calls</span>
                    </label>
                    <label class="reason-item hidden-reason extra-reason">
                        <input type="radio" name="cancelReasonRadio" value="Driver is far away from pickup location">
                        <span>Driver is far away from pickup location</span>
                    </label>
                    <label class="reason-item hidden-reason extra-reason">
                        <input type="radio" name="cancelReasonRadio" value="Found another cab / alternative transport">
                        <span>Found another cab / alternative transport</span>
                    </label>
                    <label class="reason-item hidden-reason extra-reason">
                        <input type="radio" name="cancelReasonRadio" value="Pickup location entered incorrectly">
                        <span>Pickup location entered incorrectly</span>
                    </label>
                    <label class="reason-item hidden-reason extra-reason">
                        <input type="radio" name="cancelReasonRadio" value="Destination changed">
                        <span>Destination changed</span>
                    </label>
                    <label class="reason-item hidden-reason extra-reason">
                        <input type="radio" name="cancelReasonRadio" value="Waiting time is too long">
                        <span>Waiting time is too long</span>
                    </label>
                    <label class="reason-item hidden-reason extra-reason">
                        <input type="radio" name="cancelReasonRadio" value="Driver cancelled earlier / reliability issue">
                        <span>Driver cancelled earlier / reliability issue</span>
                    </label>
                    <label class="reason-item hidden-reason extra-reason">
                        <input type="radio" name="cancelReasonRadio" value="Vehicle details not matching">
                        <span>Vehicle details not matching</span>
                    </label>
                    <label class="reason-item hidden-reason extra-reason">
                        <input type="radio" name="cancelReasonRadio" value="Safety concerns">
                        <span>Safety concerns</span>
                    </label>
                    <label class="reason-item hidden-reason extra-reason">
                        <input type="radio" name="cancelReasonRadio" value="Personal emergency">
                        <span>Personal emergency</span>
                    </label>
                    <label class="reason-item hidden-reason extra-reason">
                        <input type="radio" name="cancelReasonRadio" value="Traffic delay / route issue">
                        <span>Traffic delay / route issue</span>
                    </label>
                    <label class="reason-item hidden-reason extra-reason">
                        <input type="radio" name="cancelReasonRadio" value="App / technical issue">
                        <span>App / technical issue</span>
                    </label>
                    <label class="reason-item hidden-reason extra-reason">
                        <input type="radio" name="cancelReasonRadio" value="Payment issue (Cash / online payment)">
                        <span>Payment issue (Cash / online payment)</span>
                    </label>
                    <label class="reason-item hidden-reason extra-reason">
                        <input type="radio" name="cancelReasonRadio" value="Weather condition (rain/flood/heat)">
                        <span>Weather condition (rain/flood/heat)</span>
                    </label>
                    <label class="reason-item hidden-reason extra-reason">
                        <input type="radio" name="cancelReasonRadio" value="Other">
                        <span>Other</span>
                    </label>
        
                    <button type="button" id="toggleReasonsBtn" class="toggle-btn" onclick="toggleReasons()">Show More</button>
                </div>
        
                <textarea id="cancelReason" placeholder="Additional comments (optional)"
                    style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #e5e7eb; box-sizing: border-box; resize: none; font-family: inherit; font-size: 14px;" rows="2"></textarea>
        
                <div style="display: flex; gap: 10px; margin-top: 14px;">
                    <button onclick="closeCancelModal()"
                        style="flex: 1; padding: 12px; border-radius: 8px; border: none; background: #e5e7eb; color: #374151; font-weight: 600; cursor: pointer; font-size: 14px;">
                        Close
                    </button>
                    <button onclick="submitCancelJob()"
                        style="flex: 1; padding: 12px; border-radius: 8px; border: none; background: #ef4444; color: white; font-weight: 600; cursor: pointer; font-size: 14px;">
                        Confirm Cancel
                    </button>
                </div>
            </div>
        </div>

        <div id="paymentDrawer">

            <div class="payment-sheet">

                <div class="payment-sheet-header">
                    <button onclick="closePaymentDrawer()">
                        <i class="bi bi-arrow-left"></i>
                    </button>
                    <h3>Fare Details</h3>
                    <span></span>
                </div>

                <div id="paymentContent">
                    Loading...
                </div>

            </div>

        </div>

    </div>

    <script>
    
        let isExpanded = false;
        
        // $('#cancelReason').hide();
        
        function toggleReasons() {
            const hiddenItems = document.querySelectorAll('.extra-reason');
            const toggleBtn = document.getElementById('toggleReasonsBtn');
            
            isExpanded = !isExpanded;
            
            hiddenItems.forEach(item => {
                // Toggles display between 'flex' (matching standard layout) and hidden
                if (isExpanded) {
                    item.classList.remove('hidden-reason');
                    
                }else{
                    
                    item.classList.add('hidden-reason');
                }
                // item.style.display = isExpanded ? '' : 'none';
            });
            
            // Update button text
            toggleBtn.textContent = isExpanded ? 'Show Less' : 'Show More';
        }
        
        document.addEventListener('DOMContentLoaded', function () {
            const commentBox = document.getElementById('cancelReason');
            const reasonItems = document.querySelectorAll('.reason-item');
        
            // 1. Hide the comment box by default on page load
            if (commentBox) {
                commentBox.style.display = 'none';
            }
        
            // 2. Add click event listener to all reason options
            reasonItems.forEach(item => {
                item.addEventListener('click', function () {
                    // Find the radio button inside the clicked row
                    const radioInput = this.querySelector('input[type="radio"]');
                    
                    if (radioInput) {
                        // If "Other" is picked, show the textarea; otherwise, hide it
                        if (radioInput.value === 'Other') {
                            commentBox.style.display = 'block';
                        } else {
                            commentBox.style.display = 'none';
                        }
                    }
                });
            });
        });

        const firebaseConfig = {
            apiKey: "AIzaSyCiRKGU2xZyZNx5-ZwweLd5cPokxJjxKzw",
            authDomain: "goride-947ed.firebaseapp.com",
            projectId: "goride-947ed",
        };

        if (!firebase.apps.length) {
            firebase.initializeApp(firebaseConfig);
        }
        const db = firebase.firestore();
        const jobNo = "{{ $job_no }}";
        const collectionName = "{{ env('FIREBASE_COLLECTION', 'jobs') }}";

        db.collection(collectionName)
            .doc(jobNo)
            .onSnapshot((doc) => {

                let data = doc.data()??[];

                // if (!data) {
                //     goInfo();
                //     return;
                // }

                if (doc.exists && !document.querySelector('.success-container')) {
                    if ("{{ $job_status }}" != 'Created' && "{{ $job_status }}" != 'Bidding' && "{{ $job_status }}" != 'Pending') {
                        goInfo();
                    }
                }

                let bids = Array.isArray(data.bids_details) ? data.bids_details : [];

                let bidsDetails = [];

                if (data.bids_details && typeof data.bids_details === 'object') {
                    bidsDetails = Object.entries(data.bids_details).map(([key, value]) => {
                        return {
                            driver_id: key,
                            ...value
                        };
                    });
                }

                // hideEmpty();

                bidsDetails.sort((a, b) => {
                    return (parseFloat(a.amount) || 0) - (parseFloat(b.amount) || 0);
                });

                document.getElementById('bidCount').innerText = bidsDetails.length + ' Bids';

                let html = '';

                var baseFare = parseFloat("{{ $base_fare }}");
                var commission = parseFloat("{{ $com }}");
                var govtLevy = parseFloat("{{ $govt_levy }}");
                var targetAmount = (baseFare - commission) + govtLevy;

                bidsDetails.forEach((bid, index) => {

                    if (parseFloat(bid.amount) == targetAmount) {
                        bid.amount = (parseFloat(bid.amount) - govtLevy) + commission;
                    } else {
                        // In JS, use Math.round()
                        let com = Math.round(parseFloat(bid.amount) * 0.05);
                        bid.amount = (parseFloat(bid.amount) - govtLevy) + com;
                    }



                    html += `
                        <div class="driver-card">
                            
                            <!-- AVATAR -->
                            <img src="${bid.b_image || 'https://via.placeholder.com/50'}" class="driver-avatar">

                            <!-- CENTER -->
                            <div class="driver-center">
                                <div class="driver-name">
                                    ${bid.b_name || 'Driver'}
                                    <span class="driver-verified"><i class="bi bi-patch-check-fill"></i></span>
                                </div>

                                <div class="driver-meta">
                                    ${bid.b_cab || ''}
                                    ${bid.b_seater ? ' • ' + bid.b_seater + ' seats' : ''}
                                    ${bid.b_luggage ? ' • Luggage ' + bid.b_luggage : ''}
                                </div>

                                <!-- MORE INFO -->
                                <div class="more-info-btn"
                                    onclick="openVehicleModal('${bid.driver_id}', '${bid.b_seater}')">
                                    More info <i class="bi bi-chevron-down"></i>
                                </div>

                                <div class="vehicle-details" id="vehicle-${bid.driver_id}"></div>
                            </div>

                            <!-- RIGHT -->
                            <div class="driver-right">
                                ${index === 0 ? `<div class="best-deal">Best</div>` : ''}

                                <div class="price">₹${bid.amount || 0}</div>

                                <button class="accept-btn"
                                    onclick="acceptDriver('${bid.driver_id}', '${bid.b_name || ''}')">
                                    Choose & Confirm Now ! <i class="bi bi-arrow-right"></i>
                                </button>
                            </div>

                        </div>
                        `;
                });

                document.getElementById('driverList').innerHTML = html;

            });

        let vehicleCache = {};

        document.getElementById('vehicleModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeVehicleModal();
            }
        });

        function openVehicleModal(driverId, seat) {

            const modal = document.getElementById('vehicleModal');
            const content = document.getElementById('modalContent');

            modal.style.display = 'flex';
            content.innerHTML = `
            <div style="text-align:center;padding:20px;">
                Loading...
            </div>
        `;

            // ✅ CACHE CHECK (important)
            if (vehicleCache[driverId]) {
                renderVehicleData(vehicleCache[driverId], seat);
                return;
            }

            fetch('/api/v1-cus/bidder-vehicle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    oid: '{{ $oid }}',
                    sid: driverId,
                    jid: '{{ $job_no }}'
                })
            })
                .then(response => {

                    // ✅ HANDLE HTTP ERRORS
                    if (!response.ok) {
                        throw new Error('Server error');
                    }

                    return response.json();
                })
                .then(res => {

                    // ✅ VALIDATE RESPONSE
                    if (!res || res.status !== true) {
                        content.innerHTML = `<div style="color:red;">${res.message || 'No vehicle data found'}</div>`;
                        return;
                    }

                    // ✅ CACHE RESPONSE
                    vehicleCache[driverId] = res;

                    // ✅ RENDER
                    renderVehicleData(res, seat);
                })
                .catch(err => {

                    console.error(err);

                    content.innerHTML = `
                                    <div style="text-align:center;padding:20px;border-radius:14px;
                                    background:#fff;box-shadow:0 6px 18px rgba(0,0,0,0.06); max-width:280px;
                                    margin:20px auto;">

                                    <div style="width:50px;height:50px;margin:0 auto 10px;border-radius:50%;
                                    background:rgba(239,68,68,0.1);display:flex;align-items:center;justify-content:center;
                                    color:#ef4444;font-size:20px;">
                                        <i class="bi bi-exclamation-triangle-fill"></i>
                                    </div>

                                    <div style="font-weight:700;font-size:14px;color:#111827;margin-bottom:4px;">
                                        Failed to load details
                                    </div>

                                    <div style="font-size:12px;color:#64748b;">
                                        Please try again
                                    </div>

                                </div>
                                `;
                });
        }

        function renderVehicleData(res, seat) {
    let v = res.data || {};
    let vehicle = v.vehicle || {};
    let q = v.vehicle_questions || {};

    let images = [];
    if (vehicle.front_view_image_url) images.push(vehicle.front_view_image_url);
    if (vehicle.back_view_image_url) images.push(vehicle.back_view_image_url);
    if (vehicle.side_view_image_url) images.push(vehicle.side_view_image_url);
    if (vehicle.interior_front_image_url) images.push(vehicle.interior_front_image_url);
    if (vehicle.interior_rear_image_url) images.push(vehicle.interior_rear_image_url);

    let html = '';

    // Hero image — no padding, bleeds to edges
    if (images.length) {
        html += `<img src="${images[0]}" class="main-vehicle-img">`;
    }

    // Everything below image in padded body
    html += `<div class="vehicle-modal-body">`;

    if (images.length > 1) {
        html += `<div class="vehicle-gallery">`;
        images.forEach(img => {
            html += `<img src="${img}" onclick="switchImage(this)">`;
        });
        html += `</div>`;
    }

    html += `<div class="vehicle-title">${v.type || 'Vehicle'}</div>`;

    if (v.admin_verify) {
        html += `<div class="vehicle-badge">✔ Verified Vehicle</div>`;
    }

    html += `
        <div class="vehicle-grid">
            <div class="vehicle-item"><b>Fuel</b>${q.fuel_type || '-'}</div>
            <div class="vehicle-item"><b>Seats</b>${seat || '-'}</div>
            <div class="vehicle-item"><b>RC Number</b>${v.rc_number || '-'}</div>
            <div class="vehicle-item"><b>Luggage</b>${v.user_info?.luggage || '-'}</div>
        </div>
    `;

    html += `</div>`; // close vehicle-modal-body

    document.getElementById('modalContent').innerHTML = html;
}

        function closeVehicleModal() {
            document.getElementById('vehicleModal').style.display = 'none';
        }


        // ===== UI HELPERS =====
        function showEmpty() {
            document.getElementById('emptyState').style.display = 'block';
            document.getElementById('driverList').innerHTML = '';
            document.getElementById('bidCount').innerText = '0 Bids';
        }

        function hideEmpty() {
            document.getElementById('emptyState').style.display = 'none';
        }

        function switchImage(el) {
            document.querySelector('.main-vehicle-img').src = el.src;
        }

        function acceptDriver(driverId, name) {

            // const confirmChoice = confirm(`Confirm booking with ${name}?`);
            // if(confirmChoice) {
            //     console.log("Accepting driver:", driverId);
            //     // Add your AJAX post here
            // }
            openPaymentDrawer(driverId);
        }

        // Cancel Job

        function openCancelModal() {
            document.getElementById('cancelModal').style.display = 'flex';
        }

        function closeCancelModal() {
            document.getElementById('cancelModal').style.display = 'none';
        }

        function submitCancelJob() {

            let reason = '';
            
            const selectedRadio = document.querySelector('input[name="cancelReasonRadio"]:checked');
            const mainReason = selectedRadio ? selectedRadio.value : "No reason selected";
            const additionalComments = document.getElementById("cancelReason").value.trim();
            
            if(mainReason == 'Other'){
                reason = additionalComments;
            }else{
                reason = mainReason;
            }

            // loading UI
            const btn = event.target;
            btn.innerText = 'Cancelling...';
            btn.disabled = true;

            fetch('/api/v1-cus/w-cancel-job', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    job_id: '{{ $job_id ?? $job_no }}', // adjust if needed
                    job_no: '{{ $job_no }}',
                    reason: reason,
                    docs: null
                })
            })
                .then(res => {
                    return res.json().then(data => {
                        if (!res.ok) {
                            return Promise.reject(data);
                        }
                        return data;
                    });
                })
                .then(res => {

                    if (!res.status) {
                        showToast("error", res.message || 'Failed to cancel', 3000);
                        btn.innerText = 'Confirm Cancel';
                        btn.disabled = false;
                        return;
                    }

                    showToast("success", 'Ride cancelled successfully', 3000);

                    closeCancelModal();

                    // redirect (important)
                    window.location.href = '/'; // change if needed

                })
                
                .catch(err => {
                    // If err is the 'data' object we rejected above, it will have .message
                    let errorMsg = err.message || 'Something went wrong';
                
                    // Handle string errors or network errors
                    if (typeof err === 'string') {
                        errorMsg = err;
                    }
                
                    showToast("error", errorMsg, 3000);
                
                    // Reset Button
                    btn.innerText = 'Confirm Cancel';
                    btn.disabled = false;
                });
        }

        // Fare Break Down

        function openPaymentDrawer(driverId) {

            let drawer = document.getElementById('paymentDrawer');
            let content = document.getElementById('paymentContent');

            drawer.style.display = 'block';

            content.innerHTML = '<div style="padding:20px;">Loading fare...</div>';

            fetch('/api/v1-cus/w-p-break-down', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    job_no: '{{ $job_no }}',
                    user_id: driverId,
                    isCredit: null,
                    payType: null,
                    isWallet: null
                })
            })
                .then(res => res.json())
                .then(res => {

                    if (!res.status) {
                        content.innerHTML = res.message;
                        return;
                    }

                    renderPaymentUI(res.data);

                })
                .catch(() => {
                    content.innerHTML = 'Failed to load fare';
                });
        }

        function renderPaymentUI(data) {

            let subtotal = data.base_fare - (data.discount || 0);

            let html = `
        <div class="payment-container">
    
            <!-- CARD -->
            <div class="payment-card">
    
                <div class="section-title">Payment Breakdown</div>
    
                <div class="row">
                    <span>Base Fare</span>
                    <b>₹${data.base_fare}</b>
                </div>
    
                ${data.discount ? `
                <div class="row green">
                    <span>Credit Bonus</span>
                    <b>-₹${data.discount}</b>
                </div>` : ''}
    
                <div class="divider"></div>
    
                <div class="row">
                    <span>Subtotal</span>
                    <b>₹${subtotal}</b>
                </div>
    
                <div class="row">
                    <span>GST (5%)</span>
                    <b>₹${data.tax}</b>
                </div>
    
                <div class="row">
                    <span>Toll Fare</span>
                    <b>₹${data.toll_fare}</b>
                </div>
    
                <div class="divider"></div>
    
                <div class="row total">
                    <span>Estimated Fare</span>
                    <b>₹${data.total_fare}</b>
                </div>
    
                <div class="no-charge">✔ No hidden charges</div>
    
            </div>
    
            <!-- CONFIRM -->
            <button class="confirm-btn" onclick="confirmPayment(event, '${data.job_no}', ${data.total_fare})">
                Pay ₹${data.total_fare} to Driver
            </button>
    
        </div>
        `;

            document.getElementById('paymentContent').innerHTML = html;
        }

        function closePaymentDrawer() {
            document.getElementById('paymentDrawer').style.display = 'none';
        }

        // Payment

        // function confirmPayment(payNo, cl) {

        //     const btn = event.target;
        //     btn.innerText = 'Processing...';
        //     btn.disabled = true;

        //     fetch('/api/v1-cus/w-ctd-payment', {
        //         method: 'POST',
        //         headers: {
        //             'Content-Type': 'application/json',
        //             'X-CSRF-TOKEN': '{{ csrf_token() }}',
        //             'Accept': 'application/json'
        //         },
        //         body: JSON.stringify({
        //             pay_no: payNo,
        //             credit_pay: null
        //         })
        //     })
        //     .then(res => {
        //         if (!res.ok) throw new Error('Failed');
        //         return res.json();
        //     })
        //     .then(res => {

        //         if (!res.status) {
        //             showToast("error", res.message || 'Payment failed', 3000);
        //             btn.innerText = 'Confirm Ride';
        //             btn.disabled = false;
        //             return;
        //         }

        //         showSuccessScreen(res.data);

        //     })
        //     .catch(() => {
        //         showToast("error", 'Something went wrong', 3000);
        //         btn.innerText = 'Confirm Ride';
        //         btn.disabled = false;
        //     });
        // }

        function confirmPayment(event, payNo, cl) {

            if (event) event.preventDefault();

            const btn = event.currentTarget;
            const originalText = btn.innerText;

            btn.innerText = 'Processing...';
            btn.disabled = true;

            fetch('/api/v1-cus/w-ctd-payment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    pay_no: payNo,
                    credit_pay: null
                })
            })
                .then(res => {

                    return res.json().then(data => {
                        if (!res.ok) return Promise.reject(data);
                        return data;
                    });
                })
                .then(res => {

                    if (res.status === "failed" || res.status == false) {
                        showToast("error", res.message || 'Payment failed', 3000);
                        btn.innerText = `Pay ₹${cl} to Driver`;
                        btn.disabled = false;
                        location.reload()
                        return;
                    }

                    showSuccessScreen(res.data || res);

                })
                .catch((err) => {

                    const errorMsg = err.message || err.error || 'Something went wrong';
                    showToast("error", errorMsg, 3000);

                    btn.innerText = `Pay ₹${cl} to Driver`;
                    btn.disabled = false;
                });
        }

        function showSuccessScreen(data) {

            // Hide everything
            document.body.innerHTML = `
        
        <div class="success-container">
    
            <div class="success-card">
    
                <div class="success-icon">✔</div>
    
                <h2>Payment Confirmed</h2>
                <p>Your ride has been successfully booked</p>
    
                <div class="success-details">
                    <div><b>Job No:</b> ${data.job_no}</div>
                    <div><b>Amount Payable to Driver:</b> ₹${data.paid_amount}</div>
                </div>
    
                <button class="home-btn" onclick="goInfo()">
                    Go to Booking Information
                </button>
    
            </div>
    
        </div>
        `;
        }

        function getSlugFromUrl() {
            let path = window.location.pathname; // /fd/xxxxx
            let parts = path.split('/');

            return parts.pop(); // last value
        }

        function goHome() {
            window.location.href = '/'; // change if needed
        }

        function goInfo() {

            let slug = getSlugFromUrl();

            if (!slug) {
                showToast("error", 'Invalid booking', 3000);
                return;
            }

            window.location.href = '/booking-information/' + slug;
        }

        const showToast = (icon, title, duration = 5000, onTimerEnd) => {
            try {
                const Toast = Swal.mixin({

                    toast: true,

                    position: "top-end",

                    showConfirmButton: false,

                    timer: duration,

                    timerProgressBar: false,

                    didOpen: (toast) => {

                        toast.onmouseenter = Swal.stopTimer;

                        toast.onmouseleave = Swal.resumeTimer;

                    },

                    didClose: () => {

                        if (typeof onTimerEnd === 'function') {

                            onTimerEnd();

                        }

                    }

                });



                Toast.fire({

                    icon: icon,

                    title: title

                });
            } catch (e) {

                console.log('Error: ' + e.message);

            }
        }

    </script>

</body>

</html>