<div class="container py-4">

    <h3 class="mb-4">Report Card Preferences</h3>

    <form id="prefsForm" method="POST" enctype="multipart/form-data"
          action="/<?= $appName ?>/card-preferences/save">

        <!-- BRANDING -->
        <div class="card mb-3">

            <div class="card-header">School Branding</div>

            <div class="card-body">

                <div class="mb-3">
                    <label class="form-label">Printed Name</label>
                    <input type="text" class="form-control" name="printed_name"
                           value="<?= $prefs['printed_name'] ?? '' ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" class="form-control" name="address"
                           value="<?= $prefs['address'] ?? '' ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Telephone</label>
                    <input type="text" class="form-control" name="telephone"
                           value="<?= $prefs['telephone'] ?? '' ?>">
                </div>

            </div>
        </div>

        <!-- COLORS -->
        <div class="card mb-3">

            <div class="card-header">Report Card Colors</div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-6">
                        <label class="form-label">Primary Color</label>
                        <input type="color"
                               class="form-control form-control-color w-100"
                               style="height:45px"
                               name="primary_color_accent"
                               value="<?= $prefs['primary_color_accent'] ?? $default['primary_color_accent']  ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Secondary Color</label>
                        <input type="color"
                               class="form-control form-control-color w-100"
                               style="height:45px"
                               name="secondary_color_accent"
                               value="<?= $prefs['secondary_color_accent'] ?? $default['secondary_color_accent']  ?>">
                    </div>

                </div>

            </div>
        </div>

        <!-- LOGO -->
        <div class="card mb-3">

            <div class="card-header">School Logo</div>

            <div class="card-body">

                <div class="mb-3">
                    <img id="logoPreview"
                         src="<?= $prefs['logo_url'] ?? '' ?>"
                         style="max-height:120px; <?= empty($prefs['logo_url']) ? 'display:none;' : '' ?>">
                </div>

                <input type="file" class="form-control" id="logoInput"
                       name="logo" accept="image/*">

                <div class="form-check mt-3">

                    <input type="hidden" name="logo_watermark" value="0">

                    <input class="form-check-input" type="checkbox"
                           name="logo_watermark" value="1"
                           <?= !empty($prefs['logo_watermark']) ? 'checked' : '' ?>>

                    <label class="form-check-label">
                        Use logo as watermark
                    </label>

                </div>

            </div>
        </div>

        <!-- PREVIEW -->
        <div class="card mb-4">

            <div class="card-header d-flex justify-content-between align-items-center">

                <span>Report Card Preview</span>

            </div>

            <div class="card-body">

                <div id="reportPreview"
                     class="rounded p-3 bg-white"     style="max-width:800px;margin:auto; border:3px solid <?= $prefs['primary_color_accent'] ?? $default['primary_color_accent']  ?>;"
   >

                    <div class="text-center mb-3">

                        <img id="previewLogo"
                             src="<?= $prefs['logo_url'] ?? '' ?>"
                             style="height:60px; <?= empty($prefs['logo_url']) ? 'display:none;' : '' ?>">

                        <h4 id="previewSchoolName" class="mt-2">
                            <?= $prefs['printed_name'] ?? 'School Name' ?>
                        </h4>

                        <div id="previewAddress">
                            <?= $prefs['address'] ?? 'School Address' ?>
                        </div>

                        <div id="previewTelephone">
                            <?= $prefs['telephone'] ?? '' ?>
                        </div>

                    </div>

                    <div id="previewHeader"
                         class="text-black text-center p-2 rounded"
                         style="background:<?= $prefs['secondary_color_accent'] ?? $default['secondary_color_accent'] ?>a2;">

                        REPORT CARD

                    </div>

                    <div class="mt-4">

                        <p>Student Name: _______________</p>
                        <p>Class: ______________________</p>
                        <p>Position: _____________________</p>

                        <div class="table-responsive">

                            <table class="table table-bordered mt-3">

                                <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>CA</th>
                                    <th>Exam</th>
                                    <th>Total</th>
                                </tr>
                                </thead>

                                <tbody>
                                <tr>
                                    <td>Mathematics</td>
                                    <td>18</td>
                                    <td>72</td>
                                    <td>90</td>
                                </tr>
                                <tr>
                                    <td>English</td>
                                    <td>16</td>
                                    <td>65</td>
                                    <td>81</td>
                                </tr>
                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>
        </div>

                <!-- SAVE & DOWNLOAD BUTTON -->
            <div class="card-header d-flex justify-content-between align-items-center">

        <button type="button" id="savePrefs" class="btn btn-success">
            Save Preferences
        </button>


                <a id="downloadPreviewBtn"
                   href="/<?= $appName ?>/generate/student?isPreview=true"
                   class="btn btn-sm btn-outline-primary"
                   >
                    Download Preview
                </a>

            </div>



        <span id="status" class="ms-3 text-muted"></span>

    </form>
</div>

<script>

document.addEventListener('DOMContentLoaded', () => {
/*
    const downloadBtn = document.getElementById('downloadPreviewBtn');

    // OPTIONAL VERSION: keep hidden on load
    if (downloadBtn) {
        downloadBtn.style.display = 'none';
    }
*/

    const primaryColor = document.querySelector('[name="primary_color_accent"]');
    
    const secondaryColor = document.querySelector('[name="secondary_color_accent"]');
    
    const previewHeader = document.getElementById('previewHeader');
    
 
    const reportPreviewContainer = document.getElementById('reportPreview');
    
 const downloadBtn = document.getElementById('downloadPreviewBtn');
 
let secondaryTransparencyHex = "a2";

//SET primary/sec colors : redundant backUp >>  html already does this - on first page load
    if (secondaryColor && previewHeader) {
        previewHeader.style.backgroundColor = secondaryColor.value + secondaryTransparencyHex;
    }
    
   if (primaryColor && reportPreviewContainer) {
           reportPreviewContainer.style.borderColor = primaryColor.value
    }
    
    
  /***********************************
  **************************/
  
function markUnsaved() {

    if (downloadBtn) {
        downloadBtn.style.display = 'none';
    }

}  
    
    
/***************/
document
    .querySelectorAll('#prefsForm input, #prefsForm select, #prefsForm textarea')
    .forEach(el => {

        el.addEventListener('input', markUnsaved);
        el.addEventListener('change', markUnsaved);

    });
    

// LOGO PREVIEW
document.getElementById('logoInput')?.addEventListener('change', function () {

    const file = this.files[0];
    if (!file) return;

    const url = URL.createObjectURL(file);

    const logo1 = document.getElementById('logoPreview');
    const logo2 = document.getElementById('previewLogo');

    logo1.src = url;
    logo1.style.display = 'block';

    logo2.src = url;
    logo2.style.display = 'block';
});

// LIVE TEXT UPDATES
document.querySelector('[name="printed_name"]')
?.addEventListener('input', e => {
    document.getElementById('previewSchoolName').textContent = e.target.value;
});

document.querySelector('[name="address"]')
?.addEventListener('input', e => {
    document.getElementById('previewAddress').textContent = e.target.value;
});

document.querySelector('[name="telephone"]')
?.addEventListener('input', e => {
    document.getElementById('previewTelephone').textContent = e.target.value;
});

document.querySelector('[name="secondary_color_accent"]')
?.addEventListener('input', e => {
    document.getElementById('previewHeader').style.backgroundColor = e.target.value+ secondaryTransparencyHex;
});


document.querySelector('[name="primary_color_accent"]')
?.addEventListener('input', e => {
    document.getElementById('reportPreview').style.borderColor = e.target.value;
});



// SAVE
document.getElementById('savePrefs')?.addEventListener('click', async () => {

    const form = document.getElementById('prefsForm');
    const status = document.getElementById('status');

    status.className = 'ms-3 text-muted';
    status.textContent = 'Saving...';

    try {

        const res = await fetch(form.action, {
            method: 'POST',
            body: new FormData(form)
        });

        const data = await res.json();

        if (data.status === 'success') {

            status.className = 'ms-3 text-success';
            status.textContent = 'Saved successfully';

            // SHOW DOWNLOAD BUTTON AFTER SUCCESS
            if (downloadBtn) {
                downloadBtn.style.display = 'inline-block';
            }

        } else {

            status.className = 'ms-3 text-danger';
            status.textContent = data.message || 'Failed';

        }

    } catch (e) {
    
    console.log ("error", e.message)

        status.className = 'ms-3 text-danger';
        status.textContent = 'Network error';
    }

});


});

</script>















