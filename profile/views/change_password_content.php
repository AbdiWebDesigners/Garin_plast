<div class="row justify-content-center">

    <div class="col-lg-8">

        <div class="card shadow border-0">

            <div class="card-header bg-white">

                <h4 class="mb-0">

                    <i class="fa fa-key text-warning"></i>

                    تغییر رمز عبور

                </h4>

            </div>

            <div class="card-body">

                <?php if($success): ?>

                    <div class="alert alert-success">

                        <i class="fa fa-check-circle"></i>

                        <?= htmlspecialchars($success) ?>

                    </div>

                <?php endif; ?>

                <?php if($error): ?>

                    <div class="alert alert-danger">

                        <i class="fa fa-times-circle"></i>

                        <?= htmlspecialchars($error) ?>

                    </div>

                <?php endif; ?>

                <form method="post" autocomplete="off">

                    <!-- رمز فعلی -->

                    <div class="mb-4">

                        <label class="form-label">

                            رمز عبور فعلی

                        </label>

                        <div class="input-group">

                            <input

                                id="current_password"

                                type="password"

                                name="current_password"

                                class="form-control"

                                required

                            >

                            <button

                                type="button"

                                class="btn btn-outline-secondary"

                                onclick="togglePassword('current_password',this)"

                            >

                                <i class="fa fa-eye"></i>

                            </button>

                        </div>

                    </div>

                    <!-- رمز جدید -->

                    <div class="mb-3">

                        <label class="form-label">

                            رمز عبور جدید

                        </label>

                        <div class="input-group">

                            <input

                                id="new_password"

                                type="password"

                                name="new_password"

                                class="form-control"

                                required

                            >

                            <button

                                type="button"

                                class="btn btn-outline-secondary"

                                onclick="togglePassword('new_password',this)"

                            >

                                <i class="fa fa-eye"></i>

                            </button>

                        </div>

                    </div>

                    <!-- قدرت رمز -->

                    <div class="mb-3">

                        <div class="progress" style="height:8px;">

                            <div

                                id="strengthBar"

                                class="progress-bar"

                                style="width:0%;"

                            ></div>

                        </div>

                        <small

                            id="strengthText"

                            class="text-muted"

                        >

                            رمز عبور را وارد کنید.

                        </small>

                    </div>

                    <!-- قوانین -->

                    <div class="alert alert-light border">

                        <strong>

                            شرایط رمز عبور

                        </strong>

                        <ul class="mt-2 mb-0">

                            <li id="ruleLength">حداقل 8 کاراکتر</li>

                            <li id="ruleUpper">حداقل یک حرف بزرگ (A-Z)</li>

                            <li id="ruleLower">حداقل یک حرف کوچک (a-z)</li>

                            <li id="ruleNumber">حداقل یک عدد</li>

                            <li id="ruleSpecial">حداقل یک کاراکتر خاص (!@#$...)</li>

                        </ul>

                    </div>

                    <!-- تکرار -->

                    <div class="mb-4">

                        <label class="form-label">

                            تکرار رمز عبور

                        </label>

                        <div class="input-group">

                            <input

                                id="confirm_password"

                                type="password"

                                name="confirm_password"

                                class="form-control"

                                required

                            >

                            <button

                                type="button"

                                class="btn btn-outline-secondary"

                                onclick="togglePassword('confirm_password',this)"

                            >

                                <i class="fa fa-eye"></i>

                            </button>

                        </div>

                        <small

                            id="matchMessage"

                            class="text-muted"

                        ></small>

                    </div>

                    <hr>

                    <div class="d-flex gap-2">

                        <button

                            type="submit"

                            class="btn btn-warning"

                        >

                            <i class="fa fa-save"></i>

                            تغییر رمز عبور

                        </button>

                        <a

                            href="index.php"

                            class="btn btn-secondary"

                        >

                            بازگشت

                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

<script>

function togglePassword(id,btn){

    let input=document.getElementById(id);

    let icon=btn.querySelector("i");

    if(input.type==="password"){

        input.type="text";

        icon.className="fa fa-eye-slash";

    }else{

        input.type="password";

        icon.className="fa fa-eye";

    }

}

const password=document.getElementById("new_password");

const bar=document.getElementById("strengthBar");

const text=document.getElementById("strengthText");

const confirm=document.getElementById("confirm_password");

const match=document.getElementById("matchMessage");

password.addEventListener("keyup",function(){

    let value=this.value;

    let score=0;

    let rules={

        length:value.length>=8,

        upper:/[A-Z]/.test(value),

        lower:/[a-z]/.test(value),

        number:/[0-9]/.test(value),

        special:/[\W_]/.test(value)

    };

    document.getElementById("ruleLength").style.color=rules.length?"green":"red";
    document.getElementById("ruleUpper").style.color=rules.upper?"green":"red";
    document.getElementById("ruleLower").style.color=rules.lower?"green":"red";
    document.getElementById("ruleNumber").style.color=rules.number?"green":"red";
    document.getElementById("ruleSpecial").style.color=rules.special?"green":"red";

    for(let k in rules){

        if(rules[k]) score++;

    }

    let percent=score*20;

    bar.style.width=percent+"%";

    if(score<=2){

        bar.className="progress-bar bg-danger";

        text.innerHTML="رمز عبور ضعیف";

    }

    else if(score<=4){

        bar.className="progress-bar bg-warning";

        text.innerHTML="رمز عبور متوسط";

    }

    else{

        bar.className="progress-bar bg-success";

        text.innerHTML="رمز عبور قوی";

    }

});

confirm.addEventListener("keyup",function(){

    if(this.value===""){

        match.innerHTML="";

        return;

    }

    if(this.value===password.value){

        match.innerHTML="✓ رمزها یکسان هستند";

        match.className="text-success";

    }else{

        match.innerHTML="✗ رمزها یکسان نیستند";

        match.className="text-danger";

    }

});

</script>