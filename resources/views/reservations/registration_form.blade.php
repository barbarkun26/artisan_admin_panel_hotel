<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form - {{ $reservation->booking_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #ffffff !important;
            color: #000000 !important;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .italic { font-style: italic; }
        
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body class="bg-white p-8">
    <div class="no-print max-w-4xl mx-auto mb-8 bg-slate-100 p-4 rounded-xl flex justify-between items-center">
        <span class="text-sm font-medium text-slate-600">Print Preview Mode - Registration Form</span>
        <button onclick="window.print()" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-lg text-xs transition-colors">
            Print
        </button>
    </div>

    <div class="max-w-4xl mx-auto mb-8">
        <!-- Header -->
        <div class="text-center mb-6 relative">
            <!-- Logo positioning (can adjust absolute positioning if needed) -->
            <div class="absolute left-0 top-0">
                <img src="{{ asset('images/PPKDJP.png') }}" alt="Logo" class="h-16 object-contain">
            </div>
            <h1 class="text-xl font-bold uppercase">ARTISAN HOTEL</h1>
            <h2 class="text-lg font-bold italic">Formulir Pendaftaran</h2>
            <h2 class="text-lg font-bold italic">Registration Form</h2>
        </div>

        @php
            $rooms = $reservation->reservationRooms;
            $roomNumbers = $rooms->pluck('room.room_number')->implode(', ');
            $roomType = $rooms->first()->room->roomType->name ?? '';
            $noOfRooms = $rooms->count();
        @endphp

        <!-- Main Form Table -->
        <table>
            <!-- Row 1 & 2: Room Info -->
            <tr>
                <td rowspan="2" class="w-1/4 font-bold text-lg align-middle">
                    Room No.<br>
                    <span class="text-xl">{{ $roomNumbers }}</span>
                </td>
                <td class="w-1/4">
                    <div class="text-[10px]">Jumlah Tamu</div>
                    <div class="text-[10px] italic">No. of Person</div>
                    <div class="font-bold mt-1 text-sm">{{ $reservation->total_guest }}</div>
                </td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td>
                    <div class="text-[10px]">Jumlah Kamar</div>
                    <div class="text-[10px] italic">No. of Room</div>
                    <div class="font-bold mt-1 text-sm">{{ $noOfRooms }}</div>
                </td>
                <td>
                    <div class="text-[10px]">Jenis Kamar</div>
                    <div class="text-[10px] italic">Room Type</div>
                    <div class="font-bold mt-1 text-sm">{{ $roomType }}</div>
                </td>
                <td></td>
            </tr>

            <!-- Check Out Time -->
            <tr>
                <td colspan="4" class="text-center bg-gray-50 py-2">
                    <div class="font-bold">Check Out Time : 12.00 Noon</div>
                    <div>Waktu Lapor Keluar : Jam 12.00 Siang</div>
                </td>
            </tr>

            <!-- Guest Details Header -->
            <tr>
                <td colspan="3" class="bg-gray-50">
                    Harap tulis dengan huruf cetak — <span class="italic">Please print in block letters</span>
                </td>
                <td rowspan="3" class="w-1/4">
                    <div class="text-[10px]">Waktu Kedatangan</div>
                    <div class="text-[10px] italic">Arrival Time</div>
                    <div class="font-bold mt-1 text-sm">
                        {{ $reservation->checkin_date->format('H:i') }}
                    </div>
                </td>
            </tr>

            <!-- Name -->
            <tr>
                <td colspan="3">
                    <div class="text-[10px]">Nama</div>
                    <div class="text-[10px] italic mb-1">Name</div>
                    <div class="font-bold">{{ $reservation->guest->name }}</div>
                </td>
            </tr>

            <!-- Profession -->
            <tr>
                <td colspan="3">
                    <div class="text-[10px]">Pekerjaan</div>
                    <div class="text-[10px] italic mb-1">Profession</div>
                    <div class="font-bold">{{ $reservation->guest->profession }}</div>
                </td>
            </tr>

            <!-- Company -->
            <tr>
                <td colspan="3">
                    <div class="text-[10px]">Perusahaan</div>
                    <div class="text-[10px] italic mb-1">Company</div>
                    <div class="font-bold">{{ $reservation->guest->company }}</div>
                </td>
                <td rowspan="2">
                    <div class="text-[10px]">Tanggal Kedatangan</div>
                    <div class="text-[10px] italic">Arrival Date</div>
                    <div class="font-bold mt-1 text-sm">
                        {{ $reservation->checkin_date->format('d M Y') }}
                    </div>
                </td>
            </tr>

            <!-- Nationality & Passport -->
            <tr>
                <td>
                    <div class="text-[10px]">Kebangsaan</div>
                    <div class="text-[10px] italic mb-1">Nationality</div>
                    <div class="font-bold">{{ $reservation->guest->nationality }}</div>
                </td>
                <td>
                    <div class="text-[10px]">No. KTP/Passport No.</div>
                    <div class="font-bold mt-1">{{ $reservation->guest->identity_number }}</div>
                </td>
                <td>
                    <div class="text-[10px]">Tanggal Lahir</div>
                    <div class="text-[10px] italic mb-1">Birth Date</div>
                    <div class="font-bold">{{ $reservation->guest->birth_date ? \Carbon\Carbon::parse($reservation->guest->birth_date)->format('d M Y') : '' }}</div>
                </td>
            </tr>

            <!-- Address -->
            <tr>
                <td colspan="3">
                    <div class="flex justify-between">
                        <div class="w-1/2">
                            <div class="text-[10px]">Alamat</div>
                            <div class="text-[10px] italic mb-1">Address</div>
                            <div class="font-bold pr-2">{{ $reservation->guest->address }}</div>
                        </div>
                        <div class="w-1/2">
                            <div class="text-[10px]">Telephone / Phone</div>
                            <div class="text-[10px] italic mb-1">Handphone / Mobile phone</div>
                            <div class="font-bold">{{ $reservation->guest->phone }}</div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-[10px]">Email</div>
                        <div class="font-bold">{{ $reservation->guest->email }}</div>
                    </div>
                </td>
                <td>
                    <div class="text-[10px]">Tgl Keberangkatan</div>
                    <div class="text-[10px] italic">Departure Date</div>
                    <div class="font-bold mt-1 text-sm">
                        {{ $reservation->checkout_date->format('d M Y') }}
                    </div>
                </td>
            </tr>

            <!-- Payment & Member -->
            <tr>
                <td colspan="3">
                    <div class="flex justify-between">
                        <div class="w-1/2">
                            <div class="text-[10px]">No. Member / Member Card No.</div>
                            <div class="font-bold mt-1">{{ $reservation->guest->member_card_no }}</div>
                        </div>
                        <div class="w-1/2">
                            <div class="text-[10px]">Cara Pembayaran</div>
                            <div class="text-[10px] italic mb-1">Method of Payment</div>
                            <div class="text-xs">
                                <label class="inline-flex items-center mr-4">
                                    <input type="checkbox" class="mr-1"> VISA
                                </label>
                                <label class="inline-flex items-center mr-4">
                                    <input type="checkbox" class="mr-1"> Debit Card
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="mr-1"> Other
                                </label>
                            </div>
                        </div>
                    </div>
                </td>
                <td></td>
            </tr>

            <!-- Agreement & Signature -->
            <tr>
                <td colspan="3" class="text-[9px] leading-tight text-justify p-4">
                    <p class="mb-2">Kepada Artisan Hotel, Saya menyatakan bahwa saya baik sendiri ataupun bersama-sama dengan perusahaan, asosiasi, perorangan atau semuanya bertanggung jawab atas pembayaran semua tagihan yang terjadi sehubungan dengan seluruh pelayanan yang Anda berikan sesuai formulir pendaftaran ini.</p>
                    <p class="mb-4 italic">To Artisan Hotel: I acknowledge that I'm jointly and severally liable with the fore-going person, company or association (and if more than one all of them) for payment of the amount of any charges payable or incurred in connecting with all service provided by you under registration.</p>
                    
                    <p class="mb-2">Untuk diketahui bahwa anda tidak diperkenankan untuk membawa durian ke area hotel.<br>Barang berharga (perhiasan, uang dsb) dapat anda simpan dalam brankas di kamar anda atau di kantor depan.</p>
                    <p class="mb-4 italic">Please be informed that you are not allowed to bring Durian in to your room.<br>For your valuable belonging (jewels, money, etc) could safe in the safe deposit box in your room or in the front office.</p>
                    
                    <p class="mb-1">Kamar ini bebas rokok. Denda sebesar Rp. 1.000.000,- akan ditagihkan apabila Anda merokok di kamar ini.</p>
                    <p class="italic">This Room is designed as a non-smoking room. Fine of Rp. 1.000.000,- will be charged for smoking in this room.</p>
                </td>
                <td class="text-center align-bottom h-40">
                    <div class="text-[10px] mb-20 text-left">Tanda Tangan Tamu/</div>
                    <div class="text-[10px] italic text-left">Signature</div>
                </td>
            </tr>

            <!-- Deposit Box -->
            <tr>
                <td colspan="2">
                    <div class="text-[10px]">Nomor Kotak Deposit</div>
                    <div class="text-[10px] italic mb-1">Safety Deposit Box Number</div>
                </td>
                <td>
                    <div class="text-[10px]">Dikeluarkan oleh</div>
                    <div class="text-[10px] italic mb-1">Issued</div>
                </td>
                <td>
                    <div class="text-[10px]">Tanggal</div>
                    <div class="text-[10px] italic mb-1">Date</div>
                </td>
            </tr>
        </table>

        <!-- Footer Signatures -->
        <div class="flex justify-between mt-8 text-center text-[10px]">
            <div class="w-1/3">
                <div>Melapor masuk oleh</div>
                <div class="italic mb-16">Check in by</div>
                <div class="border-t border-black w-4/5 mx-auto"></div>
            </div>
            <div class="w-1/3">
                <div>Melapor keluar oleh</div>
                <div class="italic mb-16">Check out by</div>
                <div class="border-t border-black w-4/5 mx-auto"></div>
            </div>
        </div>
    </div>
</body>
</html>
