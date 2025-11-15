<?php header('X-Robots-Tag: noindex'); ?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>آزمیال - اسم + آسترولوژی</title>
    <style>
        body {font-family: Tahoma; text-align: center; margin: 60px; background: #f0f8ff;}
        h1 {color: #1a73e8;}
        input, select {padding: 12px; width: 280px; margin: 10px; border: 1px solid #ccc; border-radius: 5px;}
        button {padding: 12px 30px; background: #1a73e8; color: white; border: none; border-radius: 5px; cursor: pointer;}
        button:hover {background: #1557b0;}
        #result {margin-top: 20px; font-weight: bold; color: #27ae60; padding: 20px; background: white; border-radius: 10px; border-left: 5px solid #1a73e8;}
        .count {margin-top: 30px; color: #7f8c8d; font-size: 14px;}
    </style>
</head>
<body>
    <h1>سلام! من دانیال هستم 🚀</h1>
    <p>اسمت + روز و ساعت تولدت رو بزن، Grok تحلیل آسترولوژی می‌کنه!</p>
    
    <input type="text" id="nameInput" placeholder="اسمت رو بنویس...">
    <br>
    <input type="text" id="birthDate" placeholder="روز تولد (مثل ۱ فروردین)">
    <br>
    <input type="text" id="birthTime" placeholder="ساعت تولد (مثل ۰۳:۴۵)">
    <br><br>
    <button onclick="saveAndAnalyze()">ثبت + تحلیل AI</button>
    <div id="result"></div>
    <div class="count" id="count"></div>

    <script>
        const API_KEY = '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';

        async function saveAndAnalyze() {
            const name = document.getElementById('nameInput').value.trim();
            const birthDate = document.getElementById('birthDate').value.trim();
            const birthTime = document.getElementById('birthTime').value.trim();

            if (!name || !birthDate || !birthTime) {
                alert('همه فیلدها رو پر کن!');
                return;
            }

            const result = document.getElementById('result');
            result.innerHTML = 'در حال تحلیل با Grok...';

            try {
                // ۱. ذخیره در دیتابیس
                await fetch('api/save.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name, birthDate, birthTime })
                });

                // ۲. تحلیل AI
                const response = await fetch('https://openrouter.ai/api/v1/chat/completions', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${API_KEY}`,
                        'Content-Type': 'application/json',
                        'HTTP-Referer': window.location.href,
                        'X-Title': 'Danial Astrology Lab'
                    },
                    body: JSON.stringify({
                        model: 'x-ai/grok-3-mini',
                        messages: [
                            { role: 'system', content: 'تو یه آسترولوژیست حرفه‌ای و تحلیل‌گر اسم. معنی اسم، برج فلکی، ویژگی‌های شخصیتی و توصیه‌های زندگی رو بگو. جذاب و کوتاه.' },
                            { role: 'user', content: `اسم: ${name}\nتولد: ${birthDate} ساعت ${birthTime}\nتحلیل کامل آسترولوژی + معنی اسم بده.` }
                        ],
                        max_tokens: 200,
                        temperature: 0.8
                    })
                });

                const data = await response.json();
                if (data.choices?.[0]) {
                    result.innerHTML = `<strong>تحلیل Grok:</strong><br>${data.choices[0].message.content}`;
                    loadCount();
                } else {
                    result.innerHTML = '<span style="color:red;">خطا در AI</span>';
                }
            } catch (err) {
                result.innerHTML = '<span style="color:red;">خطا: ' + err.message + '</span>';
            }
        }

        async function loadCount() {
            const res = await fetch('api/save.php');
            const data = await res.json();
            document.getElementById('count').innerHTML = `تا حالا <strong>${data.count}</strong> نفر ثبت‌نام کردن`;
        }

        window.onload = loadCount;
    </script>
</body>
</html>
