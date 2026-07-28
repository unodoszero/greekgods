@extends('layouts.app', ['title' => 'GreekGods | Calculator'])

@push('styles')
    @vite(['resources/css/site.css', 'resources/css/pages/calculator.css'])
@endpush

@section('content')
<x-site-nav />
<header class="calculator-hero">
        <div class="calculator-shell">
            <p class="calculator-eyebrow">FITNESS CALCULATOR</p>
            <h1>Understand your daily fitness numbers.</h1>
            <p class="calculator-intro">
                Estimate your BMI, resting energy needs, daily calorie expenditure, and protein intake
                from one clear set of measurements.
            </p>
        </div>
    </header>
<main class="calculator-main">
        <div class="calculator-shell">
            <form class="calculator-form" id="calculator-form" novalidate>
                <div class="form-heading">
                    <div>
                        <p class="calculator-eyebrow">YOUR DETAILS</p>
                        <h2>Build your estimate</h2>
                    </div>
                    <p>All calculations stay in your browser and are not saved.</p>
                </div>

                <fieldset class="form-section">
                    <legend>Personal details</legend>
                    <div class="form-grid form-grid--two">
                        <div class="field">
                            <label for="age">Age</label>
                            <input id="age" type="number" name="age" min="18" max="100" step="1" inputmode="numeric" autocomplete="bday-year" placeholder="e.g. 28" aria-describedby="age-help age-error">
                            <p class="field-help" id="age-help">For adults aged 18–100.</p>
                            <p class="field-error" id="age-error"></p>
                        </div>

                        <fieldset class="field choice-field" id="header-gender">
                            <legend>Sex used by the formula</legend>
                            <div class="segmented-control">
                                <input id="male" type="radio" value="male" name="gender">
                                <label for="male">Male</label>
                                <input id="female" type="radio" value="female" name="gender">
                                <label for="female">Female</label>
                            </div>
                            <p class="field-help">Required by the selected BMR equation.</p>
                            <p class="field-error" id="gender-error"></p>
                        </fieldset>
                    </div>
                </fieldset>

                <fieldset class="form-section">
                    <legend>Measurements</legend>
                    <div class="form-grid form-grid--two">
                        <div class="field">
                            <label for="height">Height</label>
                            <div class="measurement-control">
                                <input id="height" type="number" name="height" min="0.01" step="any" inputmode="decimal" placeholder="e.g. 175" aria-describedby="height-help height-error">
                                <select name="heightMetrics" id="heightMetric" aria-label="Height unit">
                                    <option value="cm">cm</option>
                                    <option value="in">in</option>
                                    <option value="m">m</option>
                                    <option value="ft">ft</option>
                                </select>
                            </div>
                            <p class="field-help" id="height-help">Use one decimal value in your preferred unit.</p>
                            <p class="field-error" id="height-error"></p>
                        </div>

                        <div class="field">
                            <label for="weight">Weight</label>
                            <div class="measurement-control">
                                <input id="weight" type="number" name="weight" min="0.01" step="any" inputmode="decimal" placeholder="e.g. 75" aria-describedby="weight-help weight-error">
                                <select name="weightMetrics" id="weightMetric" aria-label="Weight unit">
                                    <option value="kg">kg</option>
                                    <option value="lb">lb</option>
                                </select>
                            </div>
                            <p class="field-help" id="weight-help">Use your current body weight.</p>
                            <p class="field-error" id="weight-error"></p>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="form-section">
                    <legend>Calculation settings</legend>
                    <div class="form-grid form-grid--two">
                        <div class="field">
                            <label for="activity">Activity level</label>
                            <select name="activity" id="activity" aria-describedby="activity-help activity-error">
                                <option value="">Choose your typical activity</option>
                                <option value="sedentary">Sedentary — little or no exercise</option>
                                <option value="light">Light — exercise 1–3 times/week</option>
                                <option value="moderate">Moderate — exercise 3–5 times/week</option>
                                <option value="active">Active — intense exercise 6–7 times/week</option>
                                <option value="very_active">Very active — daily training or physical job</option>
                            </select>
                            <p class="field-help" id="activity-help">Choose the level that best reflects an average week.</p>
                            <p class="field-error" id="activity-error"></p>
                        </div>

                        <div class="field">
                            <label for="formula">BMR formula</label>
                            <select id="formula" aria-describedby="formula-help formula-error">
                                <option value="mifflin_st_jeor" selected>Mifflin–St Jeor</option>
                                <option value="revised_harris_benedict">Revised Harris–Benedict</option>
                                <option value="katch_mcardle">Katch–McArdle</option>
                            </select>
                            <p class="field-help" id="formula-help">Katch–McArdle uses your body-fat estimate.</p>
                            <p class="field-error" id="formula-error"></p>
                        </div>
                    </div>

                    <div class="katch-mcardle-container" id="katchMcardleContainer" hidden>
                        <div class="body-fat-heading">
                            <label for="bodyFat">Body-fat estimate</label>
                            <span><output id="bodyFatOutput" for="bodyFat sliderValue">20</output>%</span>
                        </div>
                        <div class="body-fat-controls">
                            <input type="range" id="bodyFat" min="1" max="70" value="20" step="1" aria-describedby="body-fat-help body-fat-error">
                            <div class="body-fat-number">
                                <input type="number" id="sliderValue" min="1" max="70" value="20" step="1" inputmode="numeric" aria-label="Body-fat percentage">
                                <span aria-hidden="true">%</span>
                            </div>
                        </div>
                        <p class="field-help" id="body-fat-help">Use an estimate between 1% and 70%.</p>
                        <p class="field-error" id="body-fat-error"></p>
                    </div>
                </fieldset>

                <aside class="calculator-notice" aria-label="Calculator eligibility">
                    <strong>Before you calculate</strong>
                    <p>This tool provides general estimates for adults. It is not intended for pregnancy,
                        breastfeeding, diagnosis, or medical treatment.</p>
                </aside>

                <button class="calculate-button" type="submit">Calculate my results</button>
            </form>

            <section class="calculator-results" id="calculator-results" hidden aria-labelledby="results-heading">
                <div class="results-heading">
                    <div>
                        <p class="calculator-eyebrow">YOUR RESULTS</p>
                        <h2 id="results-heading" tabindex="-1">Your daily estimates</h2>
                    </div>
                    <button type="button" class="edit-details-button" id="edit-details">Edit your details</button>
                </div>

                <div class="input-summary" aria-label="Inputs used in this calculation">
                    <p><span>Age</span><strong id="age-result">—</strong></p>
                    <p><span>Sex</span><strong id="gender-result">—</strong></p>
                    <p><span>Height</span><strong id="height-result">—</strong></p>
                    <p><span>Weight</span><strong id="weight-result">—</strong></p>
                    <p><span>Activity</span><strong id="activity-result">—</strong></p>
                </div>

                <div class="result-grid" aria-live="polite" aria-atomic="true">
                    <article class="result-card">
                        <p class="result-label">BODY MASS INDEX</p>
                        <p class="result-value" id="bmi-result">—</p>
                        <p class="classification-badge" id="classification-result">—</p>
                        <a href="#section-bmi">How BMI works</a>
                    </article>
                    <article class="result-card">
                        <p class="result-label">BASAL METABOLIC RATE</p>
                        <p class="result-value" id="bmr">—</p>
                        <p class="result-description">Estimated energy used by your body at rest.</p>
                        <a href="#section-bmr">How BMR works</a>
                    </article>
                    <article class="result-card">
                        <p class="result-label">DAILY EXPENDITURE</p>
                        <p class="result-value" id="tdee-result">—</p>
                        <p class="result-description">Estimated calories used on a typical day.</p>
                        <a href="#section-tdee">How TDEE works</a>
                    </article>
                    <article class="result-card">
                        <p class="result-label">DAILY PROTEIN</p>
                        <p class="result-value" id="protein-intake">—</p>
                        <p class="result-description">Estimated from your weight and activity level.</p>
                        <a href="#section-protein">How protein estimates work</a>
                    </article>
                </div>

                <div class="calorie-targets">
                    <div class="calorie-targets-heading">
                        <div>
                            <p class="calculator-eyebrow">CALORIE TARGETS</p>
                            <h3>Compare estimated daily intake</h3>
                        </div>
                        <p>Deficits are reference points, not prescriptions.</p>
                    </div>
                    <div class="target-grid">
                        <article class="target-card target-card--primary">
                            <p>Maintenance</p>
                            <strong id="maintain-weight">—</strong>
                            <span>No estimated deficit</span>
                        </article>
                        <article class="target-card">
                            <p>Moderate deficit</p>
                            <strong id="mid-weight-loss">—</strong>
                            <span>500 kcal below maintenance</span>
                        </article>
                        <article class="target-card">
                            <p>Larger deficit</p>
                            <strong id="weight-loss">—</strong>
                            <span>750 kcal below maintenance</span>
                        </article>
                        <article class="target-card">
                            <p>Maximum shown deficit</p>
                            <strong id="extreme-weight-loss">—</strong>
                            <span>1,000 kcal below maintenance</span>
                        </article>
                    </div>
                    <p class="target-warning" id="target-warning" hidden>
                        One or more targets fall below this calculator’s 1,000 kcal/day safety range.
                        Consider a smaller deficit and speak with a qualified health professional.
                    </p>
                </div>

                <aside class="estimate-disclaimer">
                    <strong>Use these numbers as a starting point.</strong>
                    <p>Actual needs vary. Gradual, sustainable changes are generally easier to maintain than rapid
                        weight loss. Consult a qualified health professional for individual guidance.</p>
                    <div class="source-links">
                        <a href="https://www.niddk.nih.gov/bwp." target="_blank" rel="noopener noreferrer">NIH calorie safety guidance</a>
                        <a href="https://www.cdc.gov/healthy-weight-growth/losing-weight/" target="_blank" rel="noopener noreferrer">CDC healthy weight guidance</a>
                    </div>
                </aside>
            </section>
        </div>
    </main>
<section class="calculator-learning" aria-labelledby="learn-heading">
        <div class="section-container">
            <div class="learning-intro">
                <p class="calculator-eyebrow">LEARN THE NUMBERS</p>
                <h2 id="learn-heading">How each estimate works</h2>
                <p>Read the formulas, ranges, and limitations behind the calculator.</p>
            </div>
            <div class="section-info" id="section-bmi">
                <h2>Body Mass Index (BMI)</h2>
                <p>
                    Body Mass Index (BMI) is an essential tool used in the fitness and healthcare industries to evaluate an individual's body weight in relation to their height. While it’s not a direct measure of body fat, BMI serves as an important screening method that helps categorize an individual’s weight status, providing insight into potential health risks. Whether you are underweight, have a healthy weight, are overweight, or obese, understanding your BMI can give you a clearer picture of how your body weight could be affecting your overall health.
                </p>
                <p>
                    Maintaining a healthy BMI is crucial because it is strongly correlated with physical and mental well-being, longevity, and the prevention of chronic diseases such as heart disease, type 2 diabetes, and hypertension. A BMI within the healthy range generally suggests that your body is at an optimal weight for maintaining peak performance, energy levels, and vitality. On the other hand, a BMI that falls outside of the healthy range may indicate the need for adjustments in diet, exercise, or lifestyle choices to reduce the risk of developing weight-related health conditions.
                </p>
                <p>
                    It’s important to note that BMI is just one piece of the puzzle. While it helps identify potential concerns, it does not take into account factors like muscle mass, bone density, or fat distribution, which can make BMI less accurate for athletes or those with a higher muscle-to-fat ratio. For example, a highly muscular individual may have a higher BMI but still have a very low body fat percentage.
                </p>
                <p>
                    The BMI formula is a simple way to determine where you fall on the BMI scale. Whether you use metric or imperial units, the calculation helps categorize your weight into one of four classifications. Let’s dive into how to calculate your BMI and understand what each category means for your health.
                </p>
                <p id="title-formula"><strong>Formula:</strong> To calculate BMI, use the following formulas:
                </p>
                <p id="title-formula"><strong>Standard Formula (Metric):</strong></p>
                <img id="bmi-formula-standard" src="/graphics/formulas/bmi-formula-standard.png" alt="BMI Formula - Metric" title="BMI Formula">
                <p id="title-formula"><strong>Imperial Formula:</strong></p>
                <img id="bmi-formula-imperial" src="/graphics/formulas/bmi-formula-imperial.png" alt="BMI Formula - Imperial" title="BMI Formula">
                <table>
                    <tr>
                        <td><strong>Classification</strong></td>
                        <td><strong>BMI Range</strong></td>
                    </tr>
                    <tr>
                        <td>Underweight</td>
                        <td>BMI 
                    </td></tr>
                    <tr>
                        <td>Healthy Weight</td>
                        <td>18.5 - 24.9</td>
                    </tr>
                    <tr>
                        <td>Overweight</td>
                        <td>25 - 29.9</td>
                    </tr>
                    <tr>
                        <td>Obese</td>
                        <td>BMI ≥ 30</td>
                    </tr>
                </table>
                <p>
                    A BMI score within the “Healthy Weight” range (18.5 to 24.9) is generally considered optimal for most individuals. However, those in the "Overweight" or "Obese" ranges may benefit from focusing on improving their diet and physical activity levels to reduce their BMI, thereby lowering the risk of associated diseases. Conversely, individuals in the "Underweight" range should consult with a healthcare professional to ensure they are receiving adequate nutrition and support for their body’s needs.
                </p>
                <p>
                    It’s important to remember that BMI is only a starting point in understanding your body composition. For a more comprehensive assessment, consider consulting a fitness or healthcare professional who can evaluate other factors, such as body fat percentage, muscle mass, and overall fitness level.
                </p>
            </div>
            <div class="section-info" id="section-bmr">
                <h2>Basal Metabolic Rate (BMR)</h2>
                <p>
                    Basal Metabolic Rate (BMR) represents the number of calories your body requires to perform basic life-sustaining functions at rest. These functions include breathing, circulation, cell production, and maintaining body temperature. BMR accounts for the majority of your daily caloric expenditure and serves as the foundation for understanding your overall energy needs.
                </p>
                <p>
                    Understanding your BMR is crucial for managing your weight and designing a personalized fitness or nutrition plan. By knowing how many calories your body naturally burns at rest, you can adjust your dietary intake and physical activity levels to achieve specific goals, such as weight loss, muscle gain, or weight maintenance. Whether you're looking to build lean muscle or shed excess fat, knowing your BMR provides a baseline for making informed decisions.
                </p>
                <p>
                    BMR is influenced by various factors, including age, gender, weight, height, and body composition. For instance, individuals with higher muscle mass tend to have a higher BMR because muscle tissue burns more calories than fat tissue. This is why strength training is often recommended for those aiming to boost their metabolism and burn calories more efficiently.
                </p>
                <p>
                    The calculation of BMR involves well-established formulas, such as the Harris-Benedict Equation, which can be used with either metric or imperial units. These formulas provide an estimate of your caloric needs based on your specific characteristics. Let’s explore how to calculate your BMR using these methods.
                </p>
                <p id="title-formula"><strong>Formula:</strong> To calculate BMR, use the following formulas:
                </p>

                <p id="title-formula"><strong>Mifflin - St Jeor Equation:</strong></p>
                <p id="title-formula"><strong>(Metric) Male:</strong></p>
                <img id="bmr-formula" src="/graphics/formulas/bmr-mifflin-formula-male-metric.png" alt="BMR Formula - Metric Male" title="BMR Formula">
                <p id="title-formula"><strong>(Imperial) Male:</strong></p>
                <img id="bmr-formula" src="/graphics/formulas/bmr-mifflin-formula-male-imperial.png" alt="BMR Formula - Imperial Male" title="BMR Formula">
                <p id="title-formula"><strong>(Metric) Female:</strong></p>
                <img id="bmr-formula" src="/graphics/formulas/bmr-mifflin-formula-female-metric.png" alt="BMR Formula - Metric Female" title="BMR Formula">
                <p id="title-formula"><strong>(Imperial) Female:</strong></p>
                <img id="bmr-formula" src="/graphics/formulas/bmr-mifflin-formula-female-imperial.png" alt="BMR Formula - Imperial Female" title="BMR Formula">

                <p id="title-formula"><strong>Revised Harris-Benedict Equation:</strong></p>
                <p id="title-formula"><strong>(Metric) Male:</strong></p>
                <img id="bmr-formula" src="/graphics/formulas/bmr-harris-formula-male-metric.png" alt="BMR Formula - Metric Male" title="BMR Formula">
                <p id="title-formula"><strong>(Imperial) Male:</strong></p>
                <img id="bmr-formula" src="/graphics/formulas/bmr-harris-formula-male-imperial.png" alt="BMR Formula - Imperial Male" title="BMR Formula">
                <p id="title-formula"><strong>(Metric) Female:</strong></p>
                <img id="bmr-formula" src="/graphics/formulas/bmr-harris-formula-female-metric.png" alt="BMR Formula - Metric Female" title="BMR Formula">
                <p id="title-formula"><strong>(Imperial) Female:</strong></p>
                <img id="bmr-formula" src="/graphics/formulas/bmr-harris-formula-female-imperial.png" alt="BMR Formula - Imperial Female" title="BMR Formula">
                
                <p id="title-formula"><strong>Katch-McArdle Equation:</strong></p>
                <p id="title-formula"><strong>Katch-McArdle Lean Body Mass (LBM) Equation:</strong></p>
                <img id="bmr-katch-lbm-formula" src="/graphics/formulas/bmr-katch-lbm-formula.png" alt="BMR Formula - Imperial" title="BMR Formula">
                <p id="title-formula"><strong>Katch-McArdle Equation:</strong></p>
                <img id="bmr-katch-formula" src="/graphics/formulas/bmr-katch-formula.png" alt="BMR Formula - Imperial" title="BMR Formula">
                <table id="bmr-activity">
                    <tr>
                        <td><strong>Activity Level</strong></td>
                        <td><strong>Multiplier</strong></td>
                    </tr>
                    <tr>
                        <td>Sedentary (little to no exercise)</td>
                        <td>BMR × 1.2</td>
                    </tr>
                    <tr>
                        <td>Lightly active (light exercise 1-3 days a week)</td>
                        <td>BMR × 1.375</td>
                    </tr>
                    <tr>
                        <td>Moderately active (moderate exercise 3-5 days a week)</td>
                        <td>BMR × 1.55</td>
                    </tr>
                    <tr>
                        <td>Very active (intense exercise 6-7 days a week)</td>
                        <td>BMR × 1.725</td>
                    </tr>
                    <tr>
                        <td>Extra active (very intense exercise or physical job)</td>
                        <td>BMR × 1.9</td>
                    </tr>
                </table>
                <p>
                    Whether your goal is to lose weight, gain muscle, or maintain your current physique, calculating your BMR and adjusting it for your activity level is a vital step. This information empowers you to take a strategic approach to your nutrition and exercise regimen, ensuring that your body gets the energy it needs while aligning with your fitness goals.
                </p>
                <p>
                    For an even more accurate understanding of your metabolic rate, consider using advanced methods such as metabolic testing or consulting with a fitness professional who can tailor a plan specific to your needs.
                </p>
            </div>
            <div class="section-info" id="section-tdee">
                <h2>Total Daily Energy Expenditure (TDEE)</h2>
                <p>
                    TDEE represents the total number of calories you burn in a day, including both your basal metabolic rate (BMR) and calories burned through activity. It's an essential metric for determining how much you should eat to maintain, lose, or gain weight.
                </p>
                <p id="title-formula"><strong>Formula to Calculate TDEE</strong></p>
                <img id="tdee-formula" src="/graphics/formulas/tdee-formula.png" alt="BMI Formula - Imperial" title="BMI Formula">
                <table id="tdee-table">
                    <tr>
                        <td><strong>Activity Level</strong></td>
                        <td><strong>Multiplier</strong></td>
                        <td><strong>Description</strong></td>
                    </tr>
                    <tr>
                        <td>Sedentary</td>
                        <td>1.2</td>
                        <td>Little or no exercise</td>
                    </tr>
                    <tr>
                        <td>Lightly Active</td>
                        <td>1.375</td>
                        <td>Light exercise/sports 1-3 days/week</td>
                    </tr>
                    <tr>
                        <td>Moderately Active</td>
                        <td>1.55</td>
                        <td>Moderate exercise/sports 3-5 days/week</td>
                    </tr>
                    <tr>
                        <td>Very Active</td>
                        <td>1.725</td>
                        <td>Hard exercise/sports 6-7 days/week</td>
                    </tr>
                    <tr>
                        <td>Extra Active</td>
                        <td>1.9</td>
                        <td>Very hard exercise, physical job, or training twice a day</td>
                    </tr>
                </table>
            </div>
            <div class="section-info" id="section-protein">
                <h2>Protein</h2>
                <p>
                    Protein is an essential macronutrient that plays a vital role in muscle repair, growth, and overall bodily function. For fitness enthusiasts, getting the right amount of protein is key to optimizing performance and recovery.
                </p>
                <p id="title-formula"><strong>Daily Protein Intake Recommendations</strong></p>
                <table>
                    <tr>
                        <td><strong>Category</strong></td>
                        <td><strong>Protein Recommendation</strong></td>
                    </tr>
                    <tr>
                        <td>Sedentary Individuals</td>
                        <td>0.8 grams per kilogram of body weight</td>
                    </tr>
                    <tr>
                        <td>Active Individuals</td>
                        <td>1.2–2.0 grams per kilogram of body weight</td>
                    </tr>
                    <tr>
                        <td>Strength Training</td>
                        <td>1.6–2.4 grams per kilogram of body weight</td>
                    </tr>
                    <tr>
                        <td>Weight Loss</td>
                        <td>2.0–2.4 grams per kilogram of body weight</td>
                    </tr>
                </table>
                <p id="title-formula"><strong>How to Calculate Protein Intake</strong></p>
                <img id="protein-intake-formula" src="/graphics/formulas/protein-intake-formula.png" alt="BMI Formula - Imperial" title="Protein Intake Formula">            
            </div>
        </div>
    </section>
<x-site-footer />
@endsection

@push('scripts')
    @vite(['resources/js/site.js', 'resources/js/pages/calculator/index.js'])
@endpush
