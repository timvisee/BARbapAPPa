<?php

/**
 * Payment service translations.
 */
return [
    'qr' => [
        'showPaymentQr' => 'Toon QR code voor betaling',
        'instruction' => 'Scan de QR code met de bank app op je mobiele telefoon om de betalingsgegevens automatisch in te vullen. Dit werkt met sommige moderne banken.',
    ],

    'manualiban' => [
        'pleaseTransferSameDescription' => 'Maak het bedrag over naar de rekening zoals hieronder beschreven staat. De beschrijving van de overboeking moet exact hetzelfde zijn en wordt gebruikt om je betaling te identificeren, anders gaat je betaling verloren.',
        'enterOwnIban' => 'Vul de IBAN in van de rekening waar vanaf je betaald, zodat we de betaling aan je account kunnen koppelen.',
        'confirmTransfer' => 'Ik bevestig dat ik het geld heb overgemaakt met de gegeven informatie.',
        'waitOnTransfer' => 'Aan het wachten op gebruikelijke vertragingen bij overboekingen tussen banken voordat een groepsbeheerder gevraagd zal worden om je betaling te controleren.',
        'waitOnReceipt' => 'Aan het wachten op een groepsbeheerder om je betaling handmatig te controleren. Dit kan een lange tijd duren.',
        'pleaseConfirmReceivedDescription' => 'Verifieer dat deze transactie ontvangen is op de bankrekening. Het bedrag, de IBAN en de beschrijving moeten overeen komen.',
        'approve' => [
            'approve' => 'Betaling goedkeuren, geld is ontvangen, alle gegevens komen overeen',
            'delay' => 'Betaling uitstellen, geld is nog niet ontvangen, vraag later opnieuw',
            'reject' => 'Betaling afwijzen, geld is niet ontvangen en zal ook in de toekomst niet ontvangen worden',
        ],
        'actionMessage' => [
            'approve' => 'Betaling goedgekeurd',
            'delay' => 'Betaling uitgesteld',
            'reject' => 'Betaling afgekeurd',
        ],
        'steps' => [
            'transfer' => 'Overboeking',
            'transferring' => 'Overboeken',
            'receipt' => 'Bevestiging',
        ],
        'stepDescriptions' => [
            'transfer' => 'Geld overmaken, IBAN invullen',
            'transferring' => 'Wacht op boeking',
            'receipt' => 'Wacht op bevestiging',
        ],
    ],
];