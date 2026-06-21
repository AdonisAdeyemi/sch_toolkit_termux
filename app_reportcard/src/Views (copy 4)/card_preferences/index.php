<div class="container py-4">

    <h3 class="mb-4">Report Card Preferences</h3>

    <form
        id="prefsForm"
        method="POST"
        enctype="multipart/form-data"
        action="/<?= $appName ?>/card-preferences/save">

        <!-- BRANDING -->
        <div class="card mb-3">

            <div class="card-header">
                School Branding
            </div>

            <div class="card-body">

                <div class="mb-3">
                    <label class="form-label">
                        Printed Name
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        name="printed_name"
                        value="<?= $prefs['printed_name'] ?? '' ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Address
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        name="address"
                        value="<?= $prefs['address'] ?? '' ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Telephone
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        name="telephone"
                        value="<?= $prefs['telephone'] ?? '' ?>">
                </div>

            </div>

        </div>

        <!-- COLORS -->
        <div class="card mb-3">

            <div class="card-header">
                Report Card Colors
            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-6">

                        <label class="form-label">
                            Primary Color
                        </label>

                        <input
                            type="color"
                            class="form-control form-control-color"
                            name="primary_color_accent"
                            value="<?= $prefs['primary_color_accent'] ?? '#0066cc' ?>">

                    </div>

                    <div class="col-md-6">

                        <label class="form-label">
                            Secondary Color
                        </label>

                        <input
                            type="color"
                            class="form-control form-control-color"
                            name="secondary_color_accent"
                            value="<?= $prefs['secondary_color_accent'] ?? '#ffffff' ?>">

                    </div>

                </div>

            </div>

        </div>

        <!-- LOGO -->
        <div class="card mb-3">

            <div class="card-header">
                School Logo
            </div>

            <div class="card-body">

                <div class="mb-3">

                    <img
                        id="logoPreview"
                        src="<?= $prefs['logo_url'] ?? '' ?>"
                        style="
                            max-height:120px;
                            <?= empty($prefs['logo_url']) ? 'display:none;' : '' ?>
                        ">

                </div>

                <input
                    type="file"
                    class="form-control"
                    id="logoInput"
                    name="logo"
                    accept="image/*">

                <div class="form-check mt-3">

                    <input
                        type="hidden"
                        name="logo_watermark"
                        value="0">

                    <input
                        class="form-check-input"
                        type="checkbox"
                        name="logo_watermark"
                        value="1"
                        <?= !empty($prefs['logo_watermark']) ? 'checked' : '' ?>>

                    <label class="form-check-label">
                        Use logo as watermark
                    </label>

                </div>

            </div>

        </div>

        <!-- PREVIEW -->
        <div class="card mb-4">

            <div class="card-header">
                Report Card Preview
            </div>

            <div class="card-body">

                <div
                    id="reportPreview"
                    class="border rounded p-3 bg-white">

                    <div class="text-center mb-3">

                        <img
                            id="previewLogo"
                            src="<?= $prefs['logo_url'] ?? '' ?>"
                            style="
                                height:60px;
                                <?= empty($prefs['logo_url']) ? 'display:none;' : '' ?>
                            ">

                        <h4
                            id="previewSchoolName"
                            class="mt-2">
                            <?= $prefs['printed_name'] ?? 'School Name' ?>
                        </h4>

                        <div id="previewAddress">
                            <?= $prefs['address'] ?? 'School Address' ?>
                        </div>

                        <div id="previewTelephone">
                            <?= $prefs['telephone'] ?? '' ?>
                        </div>

                    </div>

                    <div
                        id="previewHeader"
                        class="text-white text-center p-2 rounded">

                        REPORT CARD

                    </div>

                    <div class="mt-4">

                        <p>
                            Student Name:
                            ______________________
                        </p>

                        <p>
                            Class:
                            ______________________
                        </p>

                        <p>
                            Position:
                            ______________________
                        </p>

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

        <button
            type="button"
            id="savePrefs"
            class="btn btn-success">
            Save Preferences
        </button>

        <span
            id="status"
            class="ms-3">
        </span>

    </form>

</div>

<script>

document.addEventListener('DOMContentLoaded', function () {

    const primaryColor =
        document.querySelector('[name="primary_color_accent"]');

    const previewHeader =
        document.getElementById('previewHeader');

    if (primaryColor && previewHeader) {
        previewHeader.style.backgroundColor =
            primaryColor.value;
    }

});


document.getElementById('logoInput')
?.addEventListener('change', function () {

    const file = this.files[0];

    if (!file) {
        return;
    }

    const preview =
        document.getElementById('logoPreview');

    const preview2 =
        document.getElementById('previewLogo');

    const url =
        URL.createObjectURL(file);

    preview.src = url;
    preview.style.display = 'block';

    preview2.src = url;
    preview2.style.display = 'block';

});


document.querySelector('[name="printed_name"]')
?.addEventListener('input', function () {

    document.getElementById(
        'previewSchoolName'
    ).textContent = this.value;

});


document.querySelector('[name="address"]')
?.addEventListener('input', function () {

    document.getElementById(
        'previewAddress'
    ).textContent = this.value;

});


document.querySelector('[name="telephone"]')
?.addEventListener('input', function () {

    document.getElementById(
        'previewTelephone'
    ).textContent = this.value;

});


document.querySelector('[name="primary_color_accent"]')
?.addEventListener('input', function () {

    document.getElementById(
        'previewHeader'
    ).style.backgroundColor = this.value;

});


document.getElementById('savePrefs')
?.addEventListener('click', async function () {

    const form =
        document.getElementById('prefsForm');

    const status =
        document.getElementById('status');

    status.className = 'ms-3 text-muted';
    status.textContent = 'Saving...';

    try {

        const response = await fetch(
            form.action,
            {
                method: 'POST',
                body: new FormData(form)
            }
        );

        const result =
            await response.json();

        if (result.status === 'success') {

            status.className =
                'ms-3 text-success';

            status.textContent =
                'Saved successfully';

        } else {

            status.className =
                'ms-3 text-danger';

            status.textContent =
                result.message || 'Save failed';

        }

    } catch (error) {

        status.className =
            'ms-3 text-danger';

        status.textContent =
            'Network error';

    }

});

</script>
