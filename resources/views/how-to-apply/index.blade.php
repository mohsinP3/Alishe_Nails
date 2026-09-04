@extends('layouts.app')

@section('title', 'How to Apply — Alishe Nails')

@section('content')

    <div class="page-header">
        <h1>How to Apply Your Press-On Nails</h1>
        <p>Follow these steps for a salon-quality finish that lasts.</p>
    </div>

    <div class="container">
        <div class="steps-list" style="max-width:760px;margin-inline:auto;">
            @php
                $steps = [
                    'Select the correct nail size' => 'Lay each nail against your natural nail and choose the closest match for every finger before starting.',
                    'Prepare natural nails' => 'Push back cuticles, lightly buff the nail surface, and wipe with the included alcohol prep pad to remove oils.',
                    'Apply adhesive or glue' => 'Apply a thin, even layer of the included liquid glue or an adhesive tab to your natural nail.',
                    'Position the press-on nail' => 'Angle the press-on nail at the cuticle line first, then press down toward the tip.',
                    'Press and hold' => 'Hold each nail firmly in place for 20–30 seconds to allow the adhesive to bond.',
                    'Removal instructions' => 'Soak in warm water for 10–15 minutes, then gently lift from the edge — never force or pry.',
                    'Nail care' => 'Moisturise your cuticles daily and store unused nails in their original box away from direct sunlight.',
                ];
            @endphp

            @foreach ($steps as $title => $description)
                <div class="step-item">
                    <div class="step-item__num">{{ $loop->iteration }}</div>
                    <div>
                        <h4 style="margin-bottom:6px;">{{ $title }}</h4>
                        <p style="margin:0;color:rgba(43,29,29,.75);">{{ $description }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="dos-donts">
            <div class="dos-donts__card dos">
                <h4>Do's</h4>
                <ul>
                    <li><i class="fa-solid fa-check" style="color:#2f7d3f;"></i> Clean and dry your natural nails thoroughly before application.</li>
                    <li><i class="fa-solid fa-check" style="color:#2f7d3f;"></i> Size each nail individually — fingers vary in width.</li>
                    <li><i class="fa-solid fa-check" style="color:#2f7d3f;"></i> Store unused sets flat in their original packaging.</li>
                    <li><i class="fa-solid fa-check" style="color:#2f7d3f;"></i> Soak nails off gently in warm water when removing.</li>
                </ul>
            </div>
            <div class="dos-donts__card donts">
                <h4>Don'ts</h4>
                <ul>
                    <li><i class="fa-solid fa-xmark" style="color:#a33;"></i> Don't apply lotion or oil to nails right before application.</li>
                    <li><i class="fa-solid fa-xmark" style="color:#a33;"></i> Don't force or pry nails off — this can damage your natural nail.</li>
                    <li><i class="fa-solid fa-xmark" style="color:#a33;"></i> Don't submerge hands in water for at least an hour after applying.</li>
                    <li><i class="fa-solid fa-xmark" style="color:#a33;"></i> Don't reuse adhesive tabs — always use a fresh one.</li>
                </ul>
            </div>
        </div>
    </div>

@endsection
