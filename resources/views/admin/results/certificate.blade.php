<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - {{ $result->user->name }} - {{ $result->exam->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8fafc;
            color: #1f2937;
        }
        
        .certificate-container {
            max-width: 800px;
            margin: 2rem auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 8px solid #14b8a6;
        }
        
        .certificate-header {
            background: linear-gradient(135deg, #14b8a6, #0d9488);
            color: white;
            padding: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .certificate-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 30px 30px;
            opacity: 0.1;
        }
        
        .certificate-logo {
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 1rem;
            color: white;
        }
        
        .certificate-title {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 2px;
            margin-bottom: 0.5rem;
        }
        
        .certificate-subtitle {
            font-size: 16px;
            opacity: 0.9;
            letter-spacing: 1px;
        }
        
        .certificate-body {
            padding: 3rem;
            text-align: center;
        }
        
        .certificate-presented-to {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 0.5rem;
        }
        
        .student-name {
            font-size: 36px;
            font-weight: 700;
            color: #14b8a6;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .certificate-text {
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 2rem;
            color: #4b5563;
        }
        
        .achievement-details {
            margin: 2rem 0;
            padding: 1.5rem;
            background: #f0fdfa;
            border-radius: 12px;
            border: 1px solid #ccfbf1;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
        }
        
        .detail-row:last-child {
            margin-bottom: 0;
        }
        
        .detail-label {
            color: #6b7280;
            font-size: 14px;
        }
        
        .detail-value {
            font-weight: 600;
            color: #1f2937;
        }
        
        .grade-badge {
            display: inline-block;
            padding: 0.5rem 1.5rem;
            background: #14b8a6;
            color: white;
            border-radius: 8px;
            font-weight: 700;
            font-size: 20px;
            margin: 1rem 0;
        }
        
        .certificate-footer {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .signature {
            text-align: center;
            flex: 1;
        }
        
        .signature-line {
            width: 200px;
            height: 1px;
            background: #1f2937;
            margin: 0.5rem auto;
        }
        
        .signature-name {
            font-weight: 600;
            margin-top: 0.5rem;
        }
        
        .signature-title {
            font-size: 12px;
            color: #6b7280;
        }
        
        .certificate-number {
            font-size: 12px;
            color: #6b7280;
            text-align: center;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px dashed #d1d5db;
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #14b8a6;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(20, 184, 166, 0.3);
            transition: all 0.3s;
            z-index: 1000;
        }
        
        .print-button:hover {
            background: #0d9488;
            transform: translateY(-2px);
        }
        
        @media print {
            .print-button {
                display: none;
            }
            
            .certificate-container {
                box-shadow: none;
                border: 8px solid #14b8a6;
                margin: 0;
                border-radius: 0;
                max-width: 100%;
            }
            
            body {
                background: white;
            }
        }
        
        @media (max-width: 768px) {
            .certificate-container {
                margin: 1rem;
            }
            
            .certificate-body {
                padding: 2rem 1rem;
            }
            
            .student-name {
                font-size: 28px;
            }
            
            .certificate-footer {
                flex-direction: column;
                gap: 2rem;
            }
            
            .print-button {
                top: 10px;
                right: 10px;
                padding: 8px 16px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <button class="print-button" onclick="window.print()">
        <i class="ri-printer-line me-2"></i> Print Certificate
    </button>
    
    <div class="certificate-container">
        <div class="certificate-header">
            <div class="certificate-logo">A+</div>
            <h1 class="certificate-title">CERTIFICATE OF ACHIEVEMENT</h1>
            <p class="certificate-subtitle">A-plus Computer Based Test</p>
        </div>
        
        <div class="certificate-body">
            <p class="certificate-presented-to">This certificate is presented to</p>
            <h2 class="student-name">{{ $result->user->name }}</h2>
            
            <p class="certificate-text">
                in recognition of successful completion and outstanding performance in
            </p>
            
            <h3 style="color: #14b8a6; margin-bottom: 1.5rem;">{{ $result->exam->name }}</h3>
            
            <div class="grade-badge">
                Grade: {{ $result->grade }} ({{ number_format($result->percentage, 1) }}%)
            </div>
            
            <div class="achievement-details">
                <div class="detail-row">
                    <span class="detail-label">Exam Date:</span>
                    <span class="detail-value">{{ $result->exam_date->format('F d, Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Score:</span>
                    <span class="detail-value">{{ $result->correct_answers }}/{{ $result->total_questions }} Correct</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Time Taken:</span>
                    <span class="detail-value">{{ $result->time_spent_minutes }} minutes</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Rank:</span>
                    <span class="detail-value">{{ $result->rank_formatted ?? 'N/A' }}</span>
                </div>
                @if($result->total_participants)
                <div class="detail-row">
                    <span class="detail-label">Total Participants:</span>
                    <span class="detail-value">{{ $result->total_participants }}</span>
                </div>
                @endif
            </div>
            
            <p style="margin-top: 2rem; font-style: italic; color: #6b7280;">
                "Success is not final, failure is not fatal: it is the courage to continue that counts."
            </p>
            
            <div class="certificate-footer">
                <div class="signature">
                    <div class="signature-line"></div>
                    <p class="signature-name">Director of Examinations</p>
                    <p class="signature-title">A-plus CBT</p>
                </div>
                
                <div class="signature">
                    <div class="signature-line"></div>
                    <p class="signature-name">Head of Academics</p>
                    <p class="signature-title">A-plus CBT</p>
                </div>
            </div>
            
            <div class="certificate-number">
                Certificate Number: {{ $result->certificate_number }}<br>
                Issued: {{ $result->certificate_issued_at->format('F d, Y') }}
            </div>
        </div>
    </div>
    
    <script>
        // Add Remix Icons for print button
        const link = document.createElement('link');
        link.href = 'https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css';
        link.rel = 'stylesheet';
        document.head.appendChild(link);
        
        // Auto-print option
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('print') === 'true') {
            window.print();
        }
    </script>
</body>
</html>