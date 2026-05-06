<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>MDCAT Admittance Card - UHS Lahore (Complete Instructions)</title>
    <!-- Tailwind CSS v3 -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
 
    <style>
        @page {
            size: A4;
            margin: 0.5cm 0.6cm;
        }
        body {
            background: white;
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
        }

        .urdu-text {
            font-family: 'Noto Nastaliq Urdu', serif;
            direction: rtl;
            text-align: justify;
            line-height: 2;
            font-size: 11px;
            word-spacing: 0.15em;
        }
        .instruction-eng {
            line-height: 2;
            word-spacing: 0.15em;
            text-align: justify;
            font-size: 11px;
        }
        }
        /* Force print colors */
        @media print {
            .print-bg-black {
                background-color: #000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .print-bg-gray {
                background-color: #e6e6e6 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .print-bg-dark {
                background-color: #333 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            body {
                margin: 0;
                padding: 0;
            }
            .break-avoid {
                break-inside: avoid;
            }
        }
    </style>
</head>
<body class="bg-white text-black p-2 max-w-4xl mx-auto text-[10.5px]">

    <div class="w-full break-avoid">
        <!-- ========== HEADER ========== -->
        <div class="flex items-center justify-between mb-2 gap-2">
            <!-- Left Logo UHS -->
            <div class="w-16 flex-shrink-0">
                <div class="bg-gray-50 rounded-sm p-0.5 flex justify-center items-center h-12">
                    <img class="max-h-14 w-auto" src="{{ storage_path('app/public/logouhs.png') }}" alt="UHS">
                </div>
            </div>
            <div class="flex-1 text-center leading-tight">
                <h1 class="text-[18px] font-extrabold text-[#004A8F] tracking-tight">University of Health Sciences, Lahore</h1>
                <p class="text-[9px] font-bold text-black mt-0">Medical & Dental College Admission Test (MDCAT 2025)</p>
                <p class="text-[9px] font-bold text-black">Admittance Card (for Candidate)</p>
            </div>
            <!-- Right Logo PMDC -->
            <div class="w-16 flex-shrink-0 flex justify-end">
                <div class="bg-gray-50 rounded-sm p-0.5 flex justify-center items-center h-12">
                    <img class="max-h-14 w-auto" src="{{ storage_path('app/public/PMDC_Logo.png') }}" alt="PMDC">
                </div>
            </div>
        </div>

        <!-- ========== QR + ROLL + CENTRE + PHOTO ========== -->
        <div class="flex flex-nowrap gap-3 items-center mb-4">
            <!-- QR -->
            <div class="w-[85px] h-[85px] flex-shrink-0 bg-white flex items-center justify-center" style="border: 1px solid black !important; padding: 4px;">
                @php
                    $qrData = implode('|', [
                        'Roll No.:' . ($student->roll_number ?? $student->sorted_roll_number_uhs ?? ''),
                        'Name:' . ($student->student_name ?? ''),
                        'Father Name:' . ($student->father_name ?? ''),
                        'CNIC:' . ($student->id_number   ?? $student->cnic ?? ''),
                        'Centre:' . ($student->centre      ?? ''),
                    ]);
                    echo \SimpleSoftwareIO\QrCode\Facades\QrCode::size(75)->margin(0)->generate($qrData);
                @endphp
            </div>
            <!-- Roll + Centre -->
            <div class="flex-1 flex flex-col gap-1.5">
                <div class="border-2 border-black bg-[#E6E6E6] py-1 text-center" style="border: 2px solid black !important;">
                    <span class="text-[14px] font-black tracking-widest uppercase">Roll No.: {{ $student->roll_number ?? $student->sorted_roll_number_uhs ?? 'PENDING' }}</span>
                </div>
                <div class="border-2 border-black flex flex-col" style="border: 2px solid black !important;">
                    <div class="bg-[#333333] text-white text-center py-1 text-[9px] font-bold tracking-widest uppercase">Examination Centre</div>
                    <div class="bg-white p-2 text-center text-[10px] font-bold leading-normal" style="word-break: break-word;">{{ $student->centre ?? $student->national_center_name ?? $student->international_center_name ?? 'CENTER NOT ALLOCATED YET' }}</div>
                </div>
            </div>
            <!-- Photo -->
            <div class="w-[85px] flex-shrink-0 bg-white overflow-hidden" style="border: 1px solid black !important; aspect-ratio: 3/4;">
                <img class="w-full h-full object-cover" src="{{ $student->picture_local_path }}" alt="candidate">
            </div>
        </div>

        <!-- ========== CANDIDATE DETAILS ========== -->
        <div class="mb-2 text-[9.5px]">
            <div class="flex border-b border-black py-0.5">
                <div class="w-1/3 font-bold">Full Name:</div>
                <div class="w-2/3">{{ $student->student_name }}</div>
            </div>
            <div class="flex border-b border-black py-0.5">
                <div class="w-1/3 font-bold">Father's / Guardian Name:</div>
                <div class="w-2/3">{{ $student->father_name }}</div>
            </div>
            <div class="flex border-b border-black py-0.5">
                <div class="w-1/3 font-bold">ID Number:</div>
                <div class="w-2/3">{{ $student->id_number }}</div>
            </div>
        </div>

        <!-- ========== URDU INSTRUCTIONS (FULL TEXT, NO LINE REMOVED) ========== -->
        <div class="bg-black text-white text-center font-bold py-0.5 text-[14px] tracking-wide mt-0.5">اہم ہدایات</div>
        <div class="urdu-text px-1 py-1 bg-gray-50 rounded-sm mb-1">
            <ol class="list-decimal pr-4 space-y-1">
                <li>
                    <span class="font-bold">شناختی دستاویزات:</span>
                    ہر امیدوار پر لازم ہے کہ وہ اس ایڈمٹنس کارڈ کی پرنٹ شدہ کاپی کے ساتھ درج ذیل میں سے کوئی ایک اصل شناختی دستاویز لازمی طور پر ہمراہ لائے: قومی شناختی کارڈ (CNIC) یا 18 سال سے کم عمر کا جیووینائل کارڈ یا سمندر پار پاکستانیوں کا شناختی کارڈ (NICOP) یا فیملی رجسٹریشن سرٹیفکیٹ (FRC) جو نادرا سے جاری شدہ ہو یا قومی پاسپورٹ، وزارت داخلہ سے جاری ہوا ہو۔ وہ امیدوار جن کے پاس صرف فارم 'ب' ہو، وہ اپنے اصلی میٹرک یا ایف۔ایس۔سی کا سرٹیفکیٹ بھی ساتھ لائیں۔ مذکورہ دستاویزات کے بغیر کسی امیدوار کو امتحان میں شرکت کی اجازت نہیں دی جائے گی۔
                </li>

                <li>
                    <span class="font-bold">وقت اور حاضری:</span>
                    امتحانی مرکز صبح 8:00 بجے کھلے گا۔ تمام امیدواروں کے لیے لازم ہے کہ وہ صبح 9:00 بجے سے پہلے اپنے امتحانی مرکز پر پہنچ جائیں۔ پرچہ تین (3) گھنٹے کا ہوگا جو 1:00 بجے دوپہر ختم ہوگا۔ کسی امیدوار کو پرچہ ختم ہونے سے پہلے امتحانی مرکز چھوڑنے کی اجازت نہیں ہوگی۔
                </li>

                <li>
                    <span class="font-bold">ممنوعہ اشیاء:</span>
                    درج ذیل اشیاء کو امتحانی مرکز میں لانا سختی سے منع ہے: موبائل فون، کیلکولیٹر، کتابیں یا نوٹس، اسلحہ یا کوئی ہتھیار، سمارٹ یا ڈیجیٹل گھڑی، کوئی بھی برقی یا رابطے کی بلیوٹوتھ ڈیوائس وغیرہ۔ اگر کسی امیدوار کے پاس ممنوعہ چیز پائی گئی تو اسے فوراً امتحانی مرکز سے نکال دیا جائے گا اور اس کا پرچہ منسوخ کر دیا جائے گا اور مزید قانونی کارروائی کے لیے حوالہ پولیس کیا جائے گا۔ ذہن نشین رہے کہ مرکز میں قیمتی اشیاء رکھنے کا کوئی انتظام نہیں ہوگا۔
                </li>

                <li>
                    <span class="font-bold">اشیاء جنہیں ساتھ لانے کی اجازت ہے:</span>
                    امیدوار اپنے ساتھ صرف درج ذیل چیزیں لا سکتے ہیں: شفاف کلپ بورڈ، دو نیلے بال پوائنٹ پین، شفاف چھوٹی پانی کی بوتل۔
                </li>

                <li>
                    <span class="font-bold">دیر سے آنے کی ممانعت:</span>
                    کسی بھی امیدوار کو صبح 9:00 بجے کے بعد امتحانی مرکز میں داخلے کی اجازت نہیں دی جائے گی۔
                </li>

                <li>
                    <span class="font-bold">والدین کے لیے ہدایت:</span>
                    والدین یا سرپرست امیدوار کو امتحان سے پہلے چھوڑیں اور پرچہ ختم ہونے کے بعد ہی لینے آئیں۔ انہیں امتحانی مرکز کے اندر یا اس کے قریب رکنے کی اجازت نہیں ہو گی۔
                </li>
            </ol>

            <p class="text-center font-bold text-[8.5px] pt-1">
                نوٹ: یہ کارڈ کمپیوٹر سے تیار شدہ ہے، اس پر کسی مہر یا دستخط کی ضرورت نہیں۔
            </p>
        </div>

        <!-- ========== ENGLISH INSTRUCTIONS (COMPLETE, ALL LINES PRESERVED) ========== -->
        <div class="bg-black text-white text-center font-bold py-0.5 text-[14px] tracking-wide mt-1">IMPORTANT INSTRUCTIONS</div>
        <div class="instruction-eng px-2 py-1 bg-gray-50 rounded-sm mb-1 text-[12px]">
            <ol class="list-decimal pl-4 space-y-0.5">
                <li>
                    <strong>Mandatory Identification:</strong>
                    Every candidate shall bring a printed copy of this Admittance Card along with one of the following original identification documents: CNIC, Juvenile Card (for age under 18), NICOP, Family Registration Certificate (FRC) issued by NADRA, or Passport issued by the Directorate General of Immigration and Passports, Ministry of Interior. Candidates who possess only a "B-Form" must also bring their original Matriculation or F.Sc. certificate. No candidate shall be permitted to enter or appear in the examination without these documents.
                </li>

                <li>
                    <strong>Timing and Reporting:</strong>
                    The Examination Centre shall open at 8:00 A.M. on Sunday, 26th October 2026. All candidates must report before 9:00 A.M. The duration of the test shall be three (03) hours, concluding at 1:00 P.M. No candidate shall be allowed to leave the centre before the conclusion of the paper.
                </li>

                <li>
                    <strong>Prohibited Items:</strong>
                    The following items are strictly prohibited within the Examination Centre premises: Mobile phones, Calculators, Books, notes, or any written material, Weapons or any arms, Smart or digital watches, Any electronic or communication Bluetooth device, etc. Possession or use of any prohibited item shall lead to immediate expulsion and cancellation of the paper under the Unfair Means Case (UMC) Regulations. Such candidates shall also be handed over to Police for legal proceedings as per law. Please note that no facility for safekeeping of valuables shall be provided at the Centre.
                </li>

                <li>
                    <strong>Permitted Items:</strong>
                    Candidates are only allowed to bring the following: Transparent clip board, Two blue ballpoint pens, Small transparent water bottle.
                </li>

                <li>
                    <strong>Entry Restriction:</strong>
                    No candidate shall be allowed entry into the Examination Centre after 9:00 A.M. under any circumstances.
                </li>

                <li>
                    <strong>Parental Instructions:</strong>
                    Parents or guardians are advised to drop the candidates before the commencement of the test and pick them up after it concludes. They are not permitted to stay inside or near the examination premises.
                </li>
            </ol>
        </div>

        <!-- ========== FOOTER NOTE (Computer generated) ========== -->
        <div class="text-center text-[7px] border-t border-gray-300 pt-1 mt-1.5 text-gray-600 flex justify-between items-center">
            <span>Note: This Admittance Card is computer-generated and does not require any stamp or signature.</span>
            <span class="font-medium">MDCAT 2026 | UHS Lahore</span>
        </div>
        
        <!-- extra hidden to avoid breaking -->
        <div class="hidden print:block break-inside-avoid"></div>
    </div>
</body>
</html>