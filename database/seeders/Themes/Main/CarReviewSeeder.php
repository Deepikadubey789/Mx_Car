<?php

namespace Database\Seeders\Themes\Main;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Supports\BaseSeeder;
use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Models\CarReview;
use Botble\CarRentals\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class CarReviewSeeder extends BaseSeeder
{
    public function run(): void
    {
        CarReview::query()->truncate();

        $customerIds = Customer::query()->pluck('id')->all();
        $carIds = Car::query()->pluck('id')->all();

        if (empty($customerIds) || empty($carIds)) {
            return;
        }

        $reviewTemplates = $this->getReviewTemplates();

        $reviewCount = rand(50, 80);

        for ($i = 0; $i < $reviewCount; $i++) {
            $star = $this->getWeightedStarRating();
            $template = Arr::random($reviewTemplates[$star]);

            CarReview::query()->create([
                'star' => $star,
                'customer_id' => Arr::random($customerIds),
                'car_id' => Arr::random($carIds),
                'content' => $this->generateReviewContent($template),
                'status' => BaseStatusEnum::PUBLISHED,
                'created_at' => Carbon::now()->subDays(rand(1, 730)),
            ]);
        }
    }

    private function getWeightedStarRating(): int
    {
        $weights = [
            1 => 5,
            2 => 10,
            3 => 20,
            4 => 35,
            5 => 30,
        ];

        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);

        $currentWeight = 0;
        foreach ($weights as $rating => $weight) {
            $currentWeight += $weight;
            if ($random <= $currentWeight) {
                return $rating;
            }
        }

        return 5;
    }

    private function getReviewTemplates(): array
    {
        return [
            1 => [
                'Terrible experience with this car. {issue} The vehicle was {condition} and {problem}. Would not recommend to anyone. Customer service was unhelpful when I reported the issues.',
                'Worst rental ever! {major_issue} The car {breakdown_issue} and I had to {consequence}. Completely ruined my {trip_type}. Avoid at all costs!',
                'Very disappointed. {quality_issue} The {car_part} was {poor_condition} and {safety_concern}. Not worth the money at all.',
                'Horrible condition vehicle. {maintenance_issue} I felt unsafe driving it. {specific_problem} Customer service was rude when I complained.',
            ],
            2 => [
                'Below expectations. {minor_issue} The car was {okay_condition} but {problem_area}. {positive_note} but overall not satisfied with the rental.',
                "Had some issues with this rental. {specific_issue} {problem_description} The price was reasonable but the quality didn't match.",
                'Not great but manageable. {issue_description} {minor_positive} Would look for other options next time.',
                'Disappointing rental experience. {problem_area} was problematic and {issue}. Some good points but too many negatives.',
            ],
            3 => [
                'Average rental experience. {neutral_comment} {minor_issue} but {positive_aspect}. Nothing special but gets the job done.',
                'Decent car for the price. {okay_feature} {minor_concern} but overall acceptable for {trip_type}. Would consider again if needed.',
                'Mixed feelings about this rental. {positive_point} but {negative_point}. {neutral_conclusion} Fair value for money.',
                'Standard rental car. {basic_positive} {minor_issue} {neutral_summary} Met basic expectations.',
            ],
            4 => [
                'Great rental experience! {positive_feature} The car was {good_condition} and {performance_praise}. {minor_issue} but overall very satisfied. Would definitely rent again.',
                'Really enjoyed driving this car. {comfort_praise} {performance_comment} {positive_experience} Only minor complaint is {small_issue}. Highly recommend!',
                'Excellent value for money. {feature_praise} {reliability_comment} {positive_trip_experience} Will book with them again for sure.',
                'Very pleased with this rental. {quality_praise} {service_praise} {recommendation} Just a small issue with {minor_concern} but nothing major.',
            ],
            5 => [
                'Outstanding rental experience! {excellent_feature} The car was {perfect_condition} and {amazing_performance}. {exceptional_service} Exceeded all expectations. Will definitely be back!',
                'Perfect car for our {trip_type}! {comfort_excellence} {performance_excellence} {service_excellence} Cannot fault anything about this rental. 10/10 would recommend!',
                'Absolutely fantastic! {feature_excellence} {reliability_excellence} {overall_excellence} This is how car rentals should be done. Thank you for an amazing experience!',
                "Best rental car I've ever had! {quality_excellence} {service_excellence} {value_excellence} Everything was perfect from start to finish. Will definitely use again!",
            ],
        ];
    }

    private function generateReviewContent(string $template): string
    {
        $replacements = [
            '{issue}' => [
                'The engine was making strange noises',
                'The air conditioning wasn\'t working properly',
                'There were scratches and dents all over',
                'The interior was dirty and smelled bad',
                'The brakes felt spongy and unsafe',
            ],
            '{major_issue}' => [
                'The car broke down on the highway',
                'The engine overheated within 2 hours',
                'The transmission was slipping badly',
                'The car wouldn\'t start after stopping for gas',
                'The steering wheel was shaking violently',
            ],
            '{problem}' => [
                'clearly hadn\'t been maintained',
                'had several warning lights on',
                'was making concerning noises',
                'felt unsafe to drive',
                'was not as described',
            ],
            '{condition}' => [
                'in poor condition',
                'dirty inside and out',
                'clearly overused',
                'not properly maintained',
                'showing significant wear',
            ],

            '{positive_feature}' => [
                'The car was clean and well-maintained',
                'Excellent fuel economy',
                'Very comfortable seats',
                'Smooth and quiet ride',
                'Great sound system',
                'Spacious interior',
                'Easy to drive and park',
            ],
            '{excellent_feature}' => [
                'The car was immaculate inside and out',
                'Incredible fuel efficiency saved us money',
                'The most comfortable seats I\'ve experienced',
                'Whisper-quiet cabin and smooth ride',
                'Premium sound system was amazing',
                'Surprisingly spacious for our family',
                'Advanced safety features gave us confidence',
            ],

            '{good_condition}' => [
                'clean and well-maintained',
                'in excellent mechanical condition',
                'spotless inside and out',
                'running smoothly',
                'very reliable',
            ],
            '{perfect_condition}' => [
                'absolutely pristine',
                'like driving a brand new car',
                'immaculate in every detail',
                'mechanically perfect',
                'showroom quality',
            ],

            '{performance_praise}' => [
                'performed flawlessly throughout our trip',
                'handled beautifully on both city and highway',
                'was very fuel efficient',
                'had plenty of power when needed',
                'was surprisingly quiet and smooth',
            ],
            '{amazing_performance}' => [
                'exceeded all performance expectations',
                'handled like a dream on every road',
                'delivered outstanding fuel economy',
                'provided effortless acceleration',
                'was remarkably quiet and refined',
            ],

            '{trip_type}' => [
                'vacation',
                'business trip',
                'weekend getaway',
                'family road trip',
                'wedding',
                'conference',
            ],

            '{minor_issue}' => [
                'The GPS was a bit outdated',
                'Could use a bit more trunk space',
                'The pickup process took a while',
                'Minor scuff on the bumper',
                'The radio presets weren\'t cleared',
            ],
            '{small_issue}' => [
                'the cup holders were a bit small',
                'the pickup location was hard to find',
                'the return process was slightly confusing',
                'one of the USB ports wasn\'t working',
                'the car manual was missing',
            ],

            '{service_praise}' => [
                'Staff was friendly and professional',
                'Quick and easy pickup process',
                'Excellent customer service',
                'Very helpful with directions',
                'Made the rental process seamless',
            ],
            '{exceptional_service}' => [
                'The staff went above and beyond to help us',
                'Incredibly professional and courteous service',
                'They made everything so easy and stress-free',
                'Outstanding customer service from start to finish',
                'The team was absolutely wonderful to work with',
            ],

            '{recommendation}' => [
                'Would definitely recommend to others',
                'Will be my go-to rental company',
                'Great choice for anyone needing a reliable car',
                'Perfect for business or leisure travel',
                'Excellent option for families',
            ],
            '{neutral_conclusion}' => [
                'Adequate for basic transportation needs',
                'Does what it\'s supposed to do',
                'Reasonable option if you\'re not picky',
                'Gets you from point A to point B',
                'Standard rental experience',
            ],
        ];

        $moreReplacements = [
            '{breakdown_issue}' => [
                'completely died on me',
                'started overheating',
                'had transmission problems',
                'wouldn\'t start',
                'had electrical issues',
            ],
            '{consequence}' => [
                'call a tow truck',
                'wait hours for assistance',
                'find alternative transportation',
                'miss important appointments',
                'pay for repairs myself',
            ],
            '{car_part}' => [
                'interior',
                'exterior',
                'engine',
                'transmission',
                'air conditioning',
                'sound system',
            ],
            '{poor_condition}' => [
                'falling apart',
                'completely worn out',
                'not functioning properly',
                'in terrible shape',
                'clearly neglected',
            ],
            '{safety_concern}' => [
                'I didn\'t feel safe driving it',
                'the brakes were concerning',
                'visibility was poor',
                'warning lights were on',
                'it felt unstable at highway speeds',
            ],
            '{positive_experience}' => [
                'Made our trip so much more enjoyable',
                'Perfect for our family vacation',
                'Handled the mountain roads beautifully',
                'Great for city driving and parking',
                'Impressed all our passengers',
            ],
            '{comfort_praise}' => [
                'Incredibly comfortable for long drives',
                'Seats were supportive and adjustable',
                'Plenty of legroom for everyone',
                'Climate control worked perfectly',
                'Very quiet cabin for conversations',
            ],
            '{value_excellence}' => [
                'Outstanding value for the price paid',
                'Premium experience at a fair price',
                'Best deal I\'ve found in years',
                'Worth every penny and more',
                'Incredible bang for your buck',
            ],

            '{specific_issue}' => [
                'The GPS system was completely outdated',
                'The car had a persistent rattling noise',
                'The air conditioning was barely working',
                'There was a strong odor in the cabin',
                'The radio kept cutting out',
            ],
            '{problem_description}' => [
                'which made navigation very difficult',
                'that was quite annoying during the drive',
                'making the trip uncomfortable in hot weather',
                'that we couldn\'t get rid of despite airing out',
                'interrupting our music and calls',
            ],
            '{quality_issue}' => [
                'The overall build quality was poor',
                'Multiple components seemed worn out',
                'The car showed signs of heavy use',
                'Several features weren\'t working properly',
                'The maintenance was clearly lacking',
            ],
            '{maintenance_issue}' => [
                'The car clearly hadn\'t been serviced recently',
                'Multiple warning lights were illuminated',
                'The oil looked like it hadn\'t been changed in months',
                'The tires were worn beyond safe limits',
                'Basic maintenance had been neglected',
            ],
            '{specific_problem}' => [
                'The windshield wipers were streaking badly',
                'One of the headlights was dimmer than the other',
                'The parking brake was sticking',
                'The turn signals were intermittent',
                'The horn wasn\'t working at all',
            ],
            '{okay_condition}' => [
                'acceptable but not impressive',
                'decent enough for basic needs',
                'adequate for the price point',
                'reasonable considering the age',
                'satisfactory for short trips',
            ],
            '{positive_note}' => [
                'The fuel economy was good',
                'The pickup process was smooth',
                'The staff was friendly enough',
                'The car was clean inside',
                'The price was competitive',
            ],
            '{minor_positive}' => [
                'At least it got us where we needed to go',
                'The trunk space was adequate',
                'The seats were reasonably comfortable',
                'The car started reliably',
                'The basic features worked fine',
            ],
            '{issue_description}' => [
                'The car felt underpowered on highways',
                'The interior was showing its age',
                'The ride quality was rougher than expected',
                'The technology was quite outdated',
                'The exterior had several minor scratches',
            ],
            '{problem_area}' => [
                'The communication with staff',
                'The vehicle\'s mechanical condition',
                'The cleanliness standards',
                'The pickup/return process',
                'The overall value proposition',
            ],

            '{neutral_comment}' => [
                'The car served its purpose adequately',
                'Nothing particularly stood out about this rental',
                'It was a standard rental car experience',
                'The vehicle met basic expectations',
                'Average car for an average price',
            ],
            '{positive_aspect}' => [
                'the fuel efficiency was decent',
                'the staff was professional',
                'the pickup was convenient',
                'the car was clean',
                'the price was fair',
            ],
            '{okay_feature}' => [
                'The interior space was adequate',
                'The sound system worked fine',
                'The air conditioning was effective',
                'The visibility was good',
                'The handling was predictable',
            ],
            '{positive_point}' => [
                'the car was reliable throughout our trip',
                'the fuel economy exceeded expectations',
                'the comfort level was quite good',
                'the technology features were helpful',
                'the overall condition was satisfactory',
            ],
            '{negative_point}' => [
                'the pickup process was slower than expected',
                'the car showed some wear and tear',
                'the customer service could be improved',
                'the vehicle felt a bit dated',
                'some features weren\'t working perfectly',
            ],
            '{basic_positive}' => [
                'The car was clean and functional',
                'No major issues during our rental',
                'Served our transportation needs',
                'Reliable for basic driving',
                'Adequate for the rental period',
            ],
            '{neutral_summary}' => [
                'A typical rental car experience',
                'Nothing exceptional but nothing terrible',
                'Standard service and vehicle quality',
                'Meets expectations without exceeding them',
                'Reasonable option for basic transportation',
            ],

            '{performance_comment}' => [
                'The engine performed well on both city and highway',
                'Smooth acceleration and responsive braking',
                'Good handling in various driving conditions',
                'Reliable performance throughout our trip',
                'The car felt stable and well-balanced',
            ],
            '{feature_praise}' => [
                'The infotainment system was user-friendly',
                'Excellent safety features gave us confidence',
                'The automatic climate control worked perfectly',
                'Great visibility from all angles',
                'The keyless entry was very convenient',
            ],
            '{reliability_comment}' => [
                'Never had a single mechanical issue',
                'Started every time without hesitation',
                'Performed consistently throughout our rental',
                'No unexpected problems or breakdowns',
                'Completely dependable for our entire trip',
            ],
            '{positive_trip_experience}' => [
                'Made our vacation stress-free and enjoyable',
                'Perfect companion for our business travel',
                'Enhanced our weekend getaway experience',
                'Reliable transportation for our family trip',
                'Contributed to a memorable road trip',
            ],
            '{quality_praise}' => [
                'The build quality was impressive',
                'Attention to detail was evident throughout',
                'High-quality materials and finishes',
                'Well-maintained and cared for vehicle',
                'Premium feel despite being a rental',
            ],

            '{comfort_excellence}' => [
                'The seats were like luxury recliners',
                'Unparalleled comfort for long-distance driving',
                'Every passenger commented on the amazing comfort',
                'Premium seating that rivals luxury vehicles',
                'Exceptional ergonomics and support',
            ],
            '{performance_excellence}' => [
                'The performance was absolutely flawless',
                'Incredible power delivery and smooth operation',
                'Outstanding handling that exceeded expectations',
                'Performance that rivals much more expensive cars',
                'Exceptional driving dynamics in all conditions',
            ],
            '{service_excellence}' => [
                'The customer service was world-class',
                'Staff went above and beyond at every step',
                'Exceptional attention to customer needs',
                'Service that sets the gold standard',
                'Outstanding professionalism and care',
            ],
            '{feature_excellence}' => [
                'Every feature worked flawlessly',
                'The technology was cutting-edge and intuitive',
                'Premium features that enhanced every moment',
                'Advanced capabilities that impressed everyone',
                'State-of-the-art amenities throughout',
            ],
            '{reliability_excellence}' => [
                'Absolutely perfect reliability record',
                'Not a single issue throughout our entire rental',
                'Performed flawlessly under all conditions',
                'Reliability that exceeded all expectations',
                'Zero problems - just pure dependability',
            ],
            '{overall_excellence}' => [
                'This rental exceeded every expectation',
                'A truly exceptional experience from start to finish',
                'Everything about this rental was perfect',
                'The best rental car experience we\'ve ever had',
                'Absolutely outstanding in every possible way',
            ],
            '{quality_excellence}' => [
                'The quality was absolutely pristine',
                'Luxury-level quality throughout',
                'Impeccable attention to every detail',
                'Quality that rivals the finest vehicles',
                'Exceptional craftsmanship and care',
            ],

            '{minor_concern}' => [
                'the pickup location being slightly hard to find',
                'a small scuff on the rear bumper',
                'the radio taking a moment to connect to Bluetooth',
                'one cup holder being a bit loose',
                'the owner\'s manual being missing',
            ],
        ];

        $allReplacements = array_merge($replacements, $moreReplacements);

        $content = $template;
        foreach ($allReplacements as $placeholder => $options) {
            if (str_contains($content, $placeholder)) {
                $replacement = is_array($options) ? Arr::random($options) : $options;
                $content = str_replace($placeholder, $replacement, $content);
            }
        }

        return $content;
    }
}
