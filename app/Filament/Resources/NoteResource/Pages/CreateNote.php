<?php

namespace App\Filament\Resources\NoteResource\Pages;

use App\Filament\Resources\NoteResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNote extends CreateRecord
{
    protected static string $resource = NoteResource::class;
    public static bool $createAnother = false;

    public function create(bool $another = false): void
    {
        $data = $this->form->getState();
        
        // Validate that PDF file is provided
        if (empty($data['pdf_path'])) {
            $this->addError('pdf_path', 'A PDF file must be uploaded for the note.');
            return;
        }

        parent::create($another);
    }

    protected function getFormActions(): array
    {
        $actions = array_filter(parent::getFormActions(), function ($action) {
            return $action->getName() !== 'createAnother';
        });
        
        // Add JavaScript validation to the create button
        if (!empty($actions)) {
            $actions[0]->extraAttributes([
                'onclick' => '
                    const pdfFile = document.querySelector("input[name=\"pdf_path\"]");
                    
                    // Clear previous errors
                    document.querySelectorAll(".validation-error").forEach(el => el.remove());
                    document.querySelectorAll("input[name=\"pdf_path\"]").forEach(field => {
                        field.style.borderColor = "";
                    });
                    
                    let hasError = false;
                    const pdfValue = pdfFile ? pdfFile.value.trim() : "";
                    
                    if (pdfValue === "") {
                        // Show error on PDF field
                        if (pdfFile) {
                            const wrapper = pdfFile.closest(".fi-fo-field-wrp");
                            if (wrapper) {
                                const errorDiv = document.createElement("div");
                                errorDiv.className = "validation-error text-red-500 text-sm mt-1";
                                errorDiv.textContent = "A PDF file must be uploaded for the note.";
                                wrapper.appendChild(errorDiv);
                                pdfFile.style.borderColor = "#ef4444";
                            }
                        }
                        
                        hasError = true;
                    }
                    
                    if (hasError) {
                        const errorField = document.querySelector("input[name=\"pdf_path\"]");
                        if (errorField) {
                            errorField.scrollIntoView({ behavior: "smooth", block: "center" });
                        }
                        return false;
                    }
                    
                    return true;
                '
            ]);
        }
        
        return $actions;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 