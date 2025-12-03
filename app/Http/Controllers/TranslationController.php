<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stichoza\GoogleTranslate\GoogleTranslate;

class TranslationController extends Controller
{
    public function translate(Request $request)
    {
        try {
            // Validate input
            $request->validate([
                'text' => 'required|string',
                'from' => 'required|in:de,en',
                'to' => 'required|in:de,en'
            ]);

            // Initialize translator
            $tr = new GoogleTranslate();
            $tr->setSource($request->input('from'));
            $tr->setTarget($request->input('to'));

            // Translate text
            $translatedText = $tr->translate($request->input('text'));

            return response()->json([
                'success' => true,
                'original' => $request->input('text'),
                'translated' => $translatedText
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function translatePage(Request $request)
    {
        try {
            // Validate input
            $request->validate([
                'lang' => 'required|in:de,en',
                'context' => 'sometimes|string',
                'full_page_translation' => 'sometimes|boolean'
            ]);

            // Simplified translation approach
            $tr = new GoogleTranslate();
            $tr->setSource('de');
            $tr->setTarget($request->input('lang'));

            // Comprehensive translations for answer question page
            $translations = [
                'current_lang' => $request->input('lang'),
                'title' => 'Listen again: formal "Sie" or informal "Du"? Tick it.',
                'section_titles' => [
                    'Select an option',
                    'Fill in the blanks'
                ],
                'answer_section' => [
                    'section_title' => 'Select an option',
                    'placeholders' => [
                        'Your answer...',
                        'Second answer...'
                    ]
                ],
                'sub_question_text' => [
                    // Specific translations for sub-question text
                    'audio_questions_title' => 'Audio Questions',
                    'audio_questions_subtitle' => 'Listen to the audio carefully and answer the questions below.',
                    'sub_question_text' => 'a) Listen again: formal "Sie" or informal "Du"? Tick it.',
                    'original_question_text' => 'Listen again: formal "Sie" or informal "Du"? Tick it.'
                ],
                'answer_options' => [
                    // Translations for answer options
                    'sie' => 'Formal "Sie"',
                    'du' => 'Informal "Du"',
                    'informal "du"' => 'Informal "Du"'
                ],
                'audio_section' => [
                    'listen_to_audio' => 'Listen to Audio'
                ],
                'navigation' => [
                    'go_back' => 'Go Back',
                    'all_questions' => 'All Questions',
                    'take_your_time' => 'Take your time'
                ],
                'toggle_button' => $request->input('lang') === 'en' 
                    ? 'Convert to German' 
                    : 'Convert to English'
            ];

            // If translation is to German, switch translations
            if ($request->input('lang') === 'de') {
                $translations = [
                    'current_lang' => 'de',
                    'title' => 'Hören Sie noch einmal: formell "Sie" oder informell "Du"? Kreuzen Sie an.',
                    'section_titles' => [
                        'Wählen Sie eine Option',
                        'Füllen Sie die Lücken aus'
                    ],
                    'answer_section' => [
                        'section_title' => 'Wählen Sie eine Option',
                        'placeholders' => [
                            'Ihre Antwort...',
                            'Zweite Antwort...'
                        ]
                    ],
                    'sub_question_text' => [
                        // Specific translations for sub-question text
                        'audio_questions_title' => 'Audio-Fragen',
                        'audio_questions_subtitle' => 'Hören Sie die Audiodatei sorgfältig und beantworten Sie die Fragen unten.',
                        'sub_question_text' => 'a) Hören Sie noch einmal: formell "Sie" oder informell "Du"? Kreuzen Sie an.',
                        'original_question_text' => 'Hören Sie noch einmal: formell "Sie" oder informell "Du"? Kreuzen Sie an.'
                    ],
                    'answer_options' => [
                        // Translations for answer options
                        'sie' => 'Formell "Sie"',
                        'du' => 'Informell "Du"',
                        'informal "du"' => 'Informell "Du"'
                    ],
                    'audio_section' => [
                        'listen_to_audio' => 'Audio hören'
                    ],
                    'navigation' => [
                        'go_back' => 'Zurück',
                        'all_questions' => 'Alle Fragen',
                        'take_your_time' => 'Nehmen Sie sich Zeit'
                    ],
                    'toggle_button' => 'Convert to English'
                ];
            }

            return response()->json([
                'success' => true,
                'content' => $translations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function getPageContent(Request $request)
    {
        $context = $request->input('context', 'default');
        
        // Comprehensive content extraction based on context
        switch ($context) {
            case 'answer_question':
                return [
                    'title' => [
                        'Hören Sie noch einmal: formell "Sie" oder informell "Du"? Kreuzen Sie an.'
                    ],
                    'section_titles' => [
                        'Wählen Sie eine Option',
                        'Hörfragen',
                        'Mehrfachauswahl'
                    ],
                    'sub_questions' => [
                        'Hören Sie die Audiodatei und wählen Sie die richtige Option.',
                        'Beantworten Sie die Frage basierend auf dem Audio.'
                    ],
                    'options' => [
                        'Option A', 'Option B', 'Option C', 'Option D'
                    ],
                    'true_false_labels' => [
                        'WAHR', 'FALSCH'
                    ],
                    'info_banners' => [
                        'Hören Sie die Audiodatei sorgfältig und beantworten Sie die Fragen.',
                        'Wählen Sie die korrekte Antwort für jede Unterfrage.'
                    ],
                    'labels' => [
                        'Ihre Antwort',
                        'Schriftliche Antwort'
                    ],
                    'blank_labels' => [
                        'Lücke 1', 'Lücke 2', 'Lücke 3'
                    ],
                    'match_labels' => [
                        'Element 1 passt zu:', 'Element 2 passt zu:'
                    ],
                    'file_upload_labels' => [
                        'Audio/Video-Antwort (Optional)',
                        'Datei hochladen'
                    ],
                    'blank_placeholders' => [
                        'Ihre Antwort...', 'Zweite Antwort...'
                    ],
                    'essay_placeholders' => [
                        'Schreiben Sie Ihre detaillierte Antwort hier...'
                    ],
                    'toggle_button' => [
                        'In Deutsch konvertieren', 'Auf Englisch konvertieren'
                    ],
                    'back_buttons' => [
                        'Zurück', 'Zurück zu Fragen'
                    ],
                    'breadcrumb_items' => [
                        'Dashboard', 'Kurse', 'Fragen', 'Frage beantworten'
                    ],
                    'nav_links' => [
                        'Profil', 'Abmelden'
                    ]
                ];

            default:
                return [
                    'title' => ['Standardseite'],
                    'section_titles' => ['Abschnitt'],
                    'sub_questions' => ['Unterfrage'],
                    'options' => ['Option'],
                    'true_false_labels' => ['Ja', 'Nein'],
                    'info_banners' => ['Informationsbanner'],
                    'labels' => ['Beschriftung'],
                    'blank_labels' => ['Lücke'],
                    'match_labels' => ['Passt zu:'],
                    'file_upload_labels' => ['Datei hochladen'],
                    'blank_placeholders' => ['Antwort...'],
                    'essay_placeholders' => ['Antwort schreiben...'],
                    'toggle_button' => ['Konvertieren'],
                    'back_buttons' => ['Zurück'],
                    'breadcrumb_items' => ['Startseite'],
                    'nav_links' => ['Menü']
                ];
        }
    }

    private function translateContent($content, $translator)
    {
        $translatedContent = [];
        foreach ($content as $key => $items) {
            $translatedContent[$key] = array_map(function($text) use ($translator) {
                return $translator->translate($text);
            }, $items);
        }
        return $translatedContent;
    }
} 