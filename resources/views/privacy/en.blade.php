@extends('layouts.app')

@section('title', 'Privacy Policy')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-12">
    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-heading text-gray-900 mb-3 sm:mb-4">Privacy Policy</h1>
        <p class="text-sm text-gray-600 mb-4">
            Last updated: {{ date('F j, Y') }}
        </p>
        <div class="mb-4">
            <a href="{{ route('privacy', ['lang' => 'nl']) }}" class="text-sm text-[#7fabc9] hover:underline">
                Lees deze pagina in het Nederlands
            </a>
        </div>
    </div>
    
    <div class="space-y-6 text-sm sm:text-base text-gray-700">
        <section class="bg-gray-50 rounded-lg p-5 sm:p-6 lg:p-8">
            <h2 class="text-xl sm:text-2xl font-heading text-gray-900 mb-3 sm:mb-4">1. Introduction</h2>
            <p class="mb-3">
                This privacy policy explains how the CEM Projects Portal ("we", "our", or "the Portal") collects, uses, and protects your personal data. The Portal is operated by the Computational and Experimental Mechanics (CEM) Division within the Faculty of Mechanical Engineering at Eindhoven University of Technology (TU/e).
            </p>
            <p class="mb-3">
                <strong>Scope:</strong> This privacy policy applies exclusively to the public frontend of the Portal (the publicly accessible website where users browse and search for projects). This policy does not apply to any administrative interfaces, backend systems, or content management systems used by administrators to manage the Portal.
            </p>
            <p>
                This policy applies to all users of the public frontend of the Portal.
            </p>
        </section>

        <section class="bg-gray-50 rounded-lg p-5 sm:p-6 lg:p-8">
            <h2 class="text-xl sm:text-2xl font-heading text-gray-900 mb-3 sm:mb-4">2. Data Controller</h2>
            <p class="mb-2">
                The data controller for this Portal is:
            </p>
            <p class="mb-2">
                <strong>Eindhoven University of Technology (TU/e)</strong><br>
                Computational and Experimental Mechanics (CEM) Division<br>
                Department of Mechanical Engineering<br>
                P.O. Box 513<br>
                5600 MB Eindhoven<br>
                The Netherlands
            </p>
            <p>
                For questions regarding this privacy policy, please contact: <strong>J. (Joris) Remmers</strong>
            </p>
        </section>

        <section class="bg-gray-50 rounded-lg p-5 sm:p-6 lg:p-8">
            <h2 class="text-xl sm:text-2xl font-heading text-gray-900 mb-3 sm:mb-4">3. Authentication and Identity Provider</h2>
            <p class="mb-3">
                The Portal uses SURF CONext for authentication via Security Assertion Markup Language (SAML). SURF CONext is a trusted identity provider service that enables secure single sign-on (SSO) for educational and research institutions in the Netherlands.
            </p>
            <p class="mb-3">
                When you log in to the Portal:
            </p>
            <ul class="list-disc list-inside mb-3 space-y-2 ml-4">
                <li>You are redirected to SURF CONext for authentication</li>
                <li>SURF CONext verifies your identity through your home institution (TU/e)</li>
                <li>Upon successful authentication, SURF CONext sends us a SAML assertion containing your identity information</li>
                <li>We receive and process only the following information from SURF CONext:
                    <ul class="list-disc list-inside ml-6 mt-2 space-y-1">
                        <li>Your persistent identifier</li>
                        <li>Your institutional affiliation</li>
                    </ul>
                </li>
            </ul>
            <p>
                We do not store your password or other authentication credentials. All authentication is handled by SURF CONext and your home institution.
            </p>
        </section>

        <section class="bg-gray-50 rounded-lg p-5 sm:p-6 lg:p-8">
            <h2 class="text-xl sm:text-2xl font-heading text-gray-900 mb-3 sm:mb-4">4. Personal Data We Collect</h2>
            <p class="mb-3">
                We do not save any personal information. The Portal operates in an anonymous manner, and all usage of the Portal is anonymous.
            </p>
            <p class="mb-3">
                Authentication through SURF CONext is used solely to verify your affiliation with TU/e. The authentication information received during authentication is used only for the purpose of verifying your TU/e affiliation and is not stored in a central database.
            </p>
            <p class="mb-3">
                Information entered using the filters on this Portal (such as project type, tags, sections, or other search criteria) is not saved to a central database. All filter usage remains anonymous and is not associated with your identity.
            </p>
            <p>
                We do not collect, store, or process any personal data beyond what is necessary for the anonymous verification of TU/e affiliation during the authentication process.
            </p>
        </section>

        <section class="bg-gray-50 rounded-lg p-5 sm:p-6 lg:p-8">
            <h2 class="text-xl sm:text-2xl font-heading text-gray-900 mb-3 sm:mb-4">5. Purpose and Legal Basis for Processing</h2>
            <p class="mb-3">
                We process authentication data for the following purpose:
            </p>
            <ul class="list-disc list-inside mb-3 space-y-2 ml-4">
                <li><strong>Affiliation Verification:</strong> To verify your affiliation with TU/e and control access to the Portal. Authentication is used solely to confirm that you are a student or staff member of TU/e. The authentication data is used only during the authentication process and is not stored (legal basis: legitimate interest in providing secure access to our services for TU/e members only)</li>
            </ul>
            <p class="mb-3">
                The Portal operates anonymously. All usage of the Portal, including browsing projects and using filters, is anonymous and no personal data is collected or stored. No profiles are created, no personal information is saved, and all interactions with the Portal remain anonymous.
            </p>
        </section>

        <section class="bg-gray-50 rounded-lg p-5 sm:p-6 lg:p-8">
            <h2 class="text-xl sm:text-2xl font-heading text-gray-900 mb-3 sm:mb-4">6. Data Sharing and Third Parties</h2>
            <p class="mb-3">
                We do not share personal data with third parties because we do not store any personal data. The authentication process with SURF CONext is used solely to verify your TU/e affiliation, and the authentication data is not stored or shared.
            </p>
            <p class="mb-3">
                We may use third-party service providers (e.g., hosting providers) for technical infrastructure, but these providers do not have access to personal data as we do not store any personal data on our systems.
            </p>
            <p>
                We do not sell, rent, or otherwise commercialize any personal data to third parties, and since we do not store personal data, there is no personal data to share.
            </p>
        </section>

        <section class="bg-gray-50 rounded-lg p-5 sm:p-6 lg:p-8">
            <h2 class="text-xl sm:text-2xl font-heading text-gray-900 mb-3 sm:mb-4">7. Data Retention</h2>
            <p class="mb-3">
                We do not retain any personal data because we do not store any personal data. The authentication data received from SURF CONext during the authentication process is used only for the purpose of verifying your TU/e affiliation and is not stored in our systems.
            </p>
            <p>
                Since all usage of the Portal is anonymous and no personal data is collected or stored, there is no personal data to retain or delete.
            </p>
        </section>

        <section class="bg-gray-50 rounded-lg p-5 sm:p-6 lg:p-8">
            <h2 class="text-xl sm:text-2xl font-heading text-gray-900 mb-3 sm:mb-4">8. Your Rights</h2>
            <p class="mb-3">
                Under the General Data Protection Regulation (GDPR) and applicable Dutch data protection laws, you have the following rights:
            </p>
            <p class="mb-3">
                However, since we do not store any personal data, most of these rights are not applicable in practice. We do not hold any personal data about you that could be accessed, rectified, erased, or ported. The authentication data is used only during the authentication process and is not stored.
            </p>
            <p class="mb-3">
                If you have questions about your rights or our data processing practices, please contact: <strong>J. (Joris) Remmers</strong>
            </p>
            <p>
                You have the right to lodge a complaint with the Dutch Data Protection Authority (Autoriteit Persoonsgegevens) if you believe your data protection rights have been violated.
            </p>
        </section>

        <section class="bg-gray-50 rounded-lg p-5 sm:p-6 lg:p-8">
            <h2 class="text-xl sm:text-2xl font-heading text-gray-900 mb-3 sm:mb-4">9. Data Security</h2>
            <p class="mb-3">
                Since we do not store any personal data in the public frontend of the Portal, there is no personal data to protect through storage security measures. However, we implement appropriate technical measures to ensure secure operation of the Portal:
            </p>
            <ul class="list-disc list-inside mb-3 space-y-2 ml-4">
                <li>Encryption of data in transit (HTTPS/TLS)</li>
                <li>Secure authentication through SURF CONext</li>
                <li>Access controls and authentication requirements for Portal access</li>
                <li>Regular security assessments and updates</li>
                <li>Secure hosting infrastructure</li>
            </ul>
            <p>
                However, no method of transmission over the internet is 100% secure, and we cannot guarantee absolute security.
            </p>
        </section>

        <section class="bg-gray-50 rounded-lg p-5 sm:p-6 lg:p-8">
            <h2 class="text-xl sm:text-2xl font-heading text-gray-900 mb-3 sm:mb-4">10. Cookies and Tracking</h2>
            <p class="mb-3">
                The Portal uses cookies and similar technologies only for technical purposes:
            </p>
            <ul class="list-disc list-inside mb-3 space-y-2 ml-4">
                <li>Maintaining your session and authentication state during your visit</li>
                <li>Ensuring the Portal functions correctly</li>
            </ul>
            <p class="mb-3">
                We do not use cookies to track your usage, store preferences, or collect personal information. All cookie usage is anonymous and session-based.
            </p>
            <p>
                You can control cookies through your browser settings, though disabling certain cookies may affect Portal functionality.
            </p>
        </section>

        <section class="bg-gray-50 rounded-lg p-5 sm:p-6 lg:p-8">
            <h2 class="text-xl sm:text-2xl font-heading text-gray-900 mb-3 sm:mb-4">11. International Data Transfers</h2>
            <p>
                Since we do not store any personal data in the public frontend of the Portal, there are no personal data transfers to consider. The authentication process with SURF CONext occurs within the European Economic Area (EEA), and no personal data is transferred outside the EEA as part of the frontend Portal functionality.
            </p>
        </section>

        <section class="bg-gray-50 rounded-lg p-5 sm:p-6 lg:p-8">
            <h2 class="text-xl sm:text-2xl font-heading text-gray-900 mb-3 sm:mb-4">12. Changes to This Policy</h2>
            <p>
                We may update this privacy policy from time to time. We will notify you of significant changes by posting the updated policy on this page and updating the "Last updated" date. We encourage you to review this policy periodically.
            </p>
        </section>

        <section class="bg-gray-50 rounded-lg p-5 sm:p-6 lg:p-8">
            <h2 class="text-xl sm:text-2xl font-heading text-gray-900 mb-3 sm:mb-4">13. Contact Information</h2>
            <p class="mb-2">
                If you have questions, concerns, or wish to exercise your data protection rights, please contact:
            </p>
            <p class="mb-2">
                <strong>J. (Joris) Remmers</strong><br>
                Computational and Experimental Mechanics (CEM) Division<br>
                Eindhoven University of Technology
            </p>
            <p>
                For technical support, please contact: <strong>B. (Bart) Verhaegh</strong>
            </p>
        </section>
    </div>
</div>
@endsection
