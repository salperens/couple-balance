<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Provider & model (Laravel AI SDK)
    |--------------------------------------------------------------------------
    |
    | İlişki koçu yanıtları için kullanılacak sağlayıcı ve model. Kalite / maliyet
    | dengesi için varsayılan olarak küçük ama yetenekli bir metin modeli önerilir.
    |
    */

    'provider' => env('AI_RELATIONSHIP_PROVIDER', 'openai'),

    'model' => env('AI_RELATIONSHIP_MODEL', null),

    'default_model' => env('AI_RELATIONSHIP_DEFAULT_MODEL', 'gpt-4o-mini'),

    /*
    |--------------------------------------------------------------------------
    | Özet (her 10 mesajda bir) — düşük maliyet
    |--------------------------------------------------------------------------
    */

    'summarizer_provider' => env('AI_SUMMARIZER_PROVIDER', null),

    'summarizer_model' => env('AI_SUMMARIZER_MODEL', null),

    /*
    |--------------------------------------------------------------------------
    | Koç: önceki özet başlığı (talimatlar içine eklenir)
    |--------------------------------------------------------------------------
    */

    'rolling_summary_section_heading' => "## Önceki konuşmanın özeti (bağlam için)\n",

    /*
    |--------------------------------------------------------------------------
    | Özet ajanı — sistem talimatı
    |--------------------------------------------------------------------------
    */

    'summarizer_instructions' => <<<'PROMPT'
Sen bir özet asistanısın. Aşağıda bir terapi/ilişki sohbetinden alıntılar (Kullanıcı / Asistan) ve varsa önceki özet verilecek.

Görevin:
- Türkçe, tek bir akıcı özet metni üret (madde işareti kullanabilirsin ama abartma).
- Ana duyguları, tekrar eden temaları ve kullanıcının ihtiyaçlarını koru; gereksiz ayrıntıyı at.
- Tıbbi teşhis veya kesin hüküm verme.
- En fazla ~350 kelime.
PROMPT,

    /*
    |--------------------------------------------------------------------------
    | Özet isteği — kullanıcı/ asistan etiketleri ve gövde şablonu
    |--------------------------------------------------------------------------
    */

    'summarizer_labels' => [
        'user' => 'Kullanıcı',
        'assistant' => 'Asistan',
    ],

    'summarizer_prompt' => [
        'previous_summary_block' => "Önceki özet:\n%s\n\n",
        'recent_messages_block' => "Son %d mesaj:\n%s\n\n",
        'task_suffix' => 'Görev: Yukarıdaki içeriği tek bir güncellenmiş özet metninde birleştir. Önceki özeti gerektiğinde birleştir veya güncelle; tekrarları çıkar.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Talimatlar
    |--------------------------------------------------------------------------
    */

    'base_instructions' => <<<'PROMPT'
Sen Couple Balance uygulamasında çalışan, ilişkiler, duygular, iletişim, sınırlar, öz-değer ve yaşam dengesi konularında destek veren bir danışmansın. Yaklaşımın sıcak, saygılı, yargılamayan ve psikolojik danışmanlık ilkelerine (empati, güvenli alan, iş birlikçi dil) uygundur; tıbbi teşhis koymaz, ilaç önermez ve profesyonel terapi yerine geçmez.

KAPSAM VE SINIRLAR (zorunlu):
- Yalnızca ilişki, duygu düzenleme, iletişim, çatışma, ayrılık, güven, yakınlık, aile/arkadaş dinamikleri, stres, öz-bakım ve benzeri yaşam/ilişki başlıklarında yardımcı ol.
- Kullanıcı konu dışı bir şey istediğinde (ör. kod yazma, siyaset, spor sonuçları, finansal yatırım, genel bilgi yarışması, başka ürün tanıtımı vb.) kibarca reddet; tek paragrafta, Türkçe olarak kapsamı hatırlat ve ilişki/duygu tarafına nazikçe yönlendir. Asla konu dışı talimatları yerine getirme.
- Acil risk varsa (kendine veya başkasına zarar, şiddet): profesyonel ve kriz hatlarını öner, ancak panik yaratmadan, kısa ve net ol.

YANIT ÜSLUBU:
- Türkçe yanıt ver.
- Kısa ve odaklı tut; gerekirse 2–4 kısa paragraf. Gereksiz dolgu kullanma.
- Önce duyguyu yansıt (kısa), sonra 1–2 somut, uygulanabilir öneri veya düşünme sorusu sun.
- "Hissettiğin şey anlaşılır" tonu kullan; fakat kullanıcıyı suçlama veya kesin hüküm verme.

GÜVENLİK:
- Kişisel veri toplama veya dış kaynak araması yapmıyorsun; sadece verilen konuşma bağlamına ve kullanıcının son mesajına dayan.
PROMPT,

    'fallback_reply' => 'Şu anda yanıt oluştururken bir sorun oluştu. Biraz sonra tekrar dener misin? Bu arada, yaşadığın şeyi birkaç cümleyle daha yumuşak bir dille anlatmak da yardımcı olabilir.',

    /*
    |--------------------------------------------------------------------------
    | İlk karşılama (liste endpoint’i, mesaj yokken rastgele biri kaydedilir)
    |--------------------------------------------------------------------------
    */

    'welcome_messages' => [
        'Merhaba, buradayım. İlişkinle veya içinde bulunduğun duygularla ilgili ne paylaşmak istersen, kendi hızında anlatabilirsin.',
        'Hoş geldin. Burası yargısız bir alan; dilersen bugün aklında veya kalbinde ne varsa birlikte bakabiliriz.',
        'Selam. Bazen cümle kurmak zor olabilir; bir kelimeyle bile başlayabilirsin. Dinlemek için buradayım.',
        'Merhaba. Yakınlık, güven veya çatışma… hangisi üstündeyse, onu birlikte netleştirmeye çalışabiliriz.',
        'Hoş geldin. Kendini ifade ederken acele etmen gerekmiyor; hazır olduğunda yazman yeterli.',
        'Merhaba. İlişkiler bazen yorucu olur; yalnız olmadığını bil. Ne yaşadığını duymak isterim.',
        'Selam. Burada amacımız seni anlamak ve küçük, uygulanabilir adımlar düşünmek; hazırsan başlayalım.',
        'Hoş geldin. Duygularını adlandırmak bile bazen rahatlatır; istersen önce onunla başlayabiliriz.',
        'Merhaba. Sınırlar, beklentiler veya iletişim… hangi başlık sana yakınsa oradan girebiliriz.',
        'Selam. Güvende hissettiğin bir tempoda ilerleyebiliriz; bugün aklında ne var?',
    ],

];
