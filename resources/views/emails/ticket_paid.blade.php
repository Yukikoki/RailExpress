<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>E-Tiket RailExpress</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f7; }
        .container { max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 16px; overflow: hidden; shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(to right, #2563eb, #4338ca); padding: 30px; text-align: center; color: white; }
        .content { padding: 30px; }
        .ticket-box { border: 2px dashed #e5e7eb; border-radius: 12px; padding: 20px; margin-top: 20px; background-color: #fafafa; }
        .badge { display: inline-block; padding: 4px 12px; background: #dcfce7; color: #166534; border-radius: 9999px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #9ca3af; }
        .row { display: flex; justify-content: space-between; margin-bottom: 15px; }
        .label { font-size: 10px; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px; font-weight: bold; }
        .value { font-size: 16px; font-weight: bold; color: #1f2937; }
        .price { font-size: 24px; color: #f97316; font-weight: 900; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1 style="margin: 0; font-size: 24px; letter-spacing: -1px;">RailExpress</h1>
            <p style="margin: 5px 0 0; opacity: 0.8;">E-Tiket Resmi Perjalanan Anda</p>
        </div>

        <!-- Body -->
        <div class="content">
            <p>Halo, <strong>{{ auth()->user()->name }}</strong>!</p>
            <p>Pembayaran Anda berhasil dikonfirmasi. Berikut adalah detail tiket kereta api Anda:</p>

            <div class="ticket-box">
                <div style="text-align: right; margin-bottom: 10px;">
                    <span class="badge">Lunas</span>
                </div>

                <div class="row">
                    <div>
                        <div class="label">Kode Booking</div>
                        <div class="value" style="font-size: 24px; color: #2563eb;">{{ $booking->booking_code }}</div>
                    </div>
                </div>

                <hr style="border: 0; border-top: 1px solid #eee; margin: 15px 0;">

                <div class="row">
                    <div>
                        <div class="label">Kereta</div>
                        <div class="value">{{ $booking->schedule->train->name }}</div>
                        <div style="font-size: 12px; color: #6b7280;">{{ $booking->schedule->train->class }} Class</div>
                    </div>
                    <div style="text-align: right;">
                        <div class="label">Nomor Kursi</div>
                        <div class="value" style="color: #4f46e5;">
                            @foreach($booking->passengers as $passenger)
                                {{ $passenger->seat_number }}{{ !$loop->last ? ', ' : '' }}
                            @endforeach
                        </div>
                    </div>
                </div>

                <div style="margin-top: 20px;">
                    <div class="label">Rute Perjalanan</div>
                    <div class="value">{{ $booking->schedule->originStation->name }} &rarr; {{ $booking->schedule->destinationStation->name }}</div>
                    <div style="font-size: 12px; color: #6b7280;">
                        Keberangkatan: {{ \Carbon\Carbon::parse($booking->schedule->departure_time)->format('d M Y, H:i') }}
                    </div>
                </div>

                <div style="margin-top: 20px; border-top: 1px solid #eee; pt: 15px; text-align: center;">
                    <div class="label">Total Pembayaran</div>
                    <div class="price">Rp {{ number_format($booking->schedule->price, 0, ',', '.') }}</div>
                </div>
            </div>

            <p style="margin-top: 30px; font-size: 14px; color: #6b7280;">
                *Silakan tunjukkan E-Tiket ini atau sebutkan Kode Booking saat melakukan boarding di stasiun.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            &copy; 2026 RailExpress - Sistem Informasi Kereta Api Terpadu.<br>
            Pesan tiket lebih mudah, cepat, dan aman.
        </div>
    </div>
</body>
</html>
