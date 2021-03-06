@php
    $app = config('app.name');
@endphp

<h3 class="ui header">{{ $app }} Terms of Service</h3>

<h4>1. Terms</h4>
<p>
    By accessing the website at
    <a href="{{ URL::to('/') }}">{{ URL::to('/') }}</a>, you are
    agreeing to be bound by these terms of service, all applicable
    laws and regulations, and agree that you are responsible for
    compliance with any applicable local laws. If you do not agree with
    any of these terms, you are prohibited from using or accessing this
    site. The materials contained in this website are protected by
    applicable copyright and trademark law.
</p>

<h4>2. Use License</h4>
<ol type="a">
    <li>
        Permission is granted to download a copy of the materials (information)
        on {{ $app }}'s website. This is the grant of a license,
        not a transfer of title, and under this license you may not:
        <ol type="i">
            <li>use the materials for any commercial purpose, or for any public
                display (commercial or non-commercial) unless otherwise noted;</li>
            <li>remove any copyright or other proprietary notations from the materials</li>
        </ol>
    </li>
    <li>
        Permission is granted to download a copy of the source code (software)
        for the {{ $app }} application. The source code may be used within lawful
        means as described in the included <a href="{{ route('license') }}">GNU AGPL-3.0</a>
        license.
    </li>
    <li>
        This license shall automatically terminate if you violate any
        of these restrictions and may be terminated by {{ $app }} at
        any time. Upon terminating your viewing of these materials or
        upon the termination of this license, you must destroy any
        downloaded materials in your possession whether in electronic
        or printed format.
    </li>
</ol>

<h4>3. Disclaimer</h4>
<ol type="a">
    <li>
        The materials on {{ $app }}'s website are provided on an
        'as is' basis. {{ $app }} makes no warranties, expressed or
        implied, and hereby disclaims and negates all other warranties
        including, without limitation, implied warranties or conditions
        of merchantability, fitness for a particular purpose, or
        non-infringement of intellectual property or other violation of
        rights.
    </li>
    <li>
        Further, {{ $app }} does not warrant or make any representations
        concerning the accuracy, likely results, or reliability of the
        use of the materials on its website or otherwise relating to
        such materials or on any sites linked to this site.
    </li>
</ol>

<h4>4. Limitations</h4>
<p>
    In no event shall {{ $app }} or its suppliers be liable for any
    damages (including, without limitation, damages for loss of data or
    profit, or due to business interruption) arising out of the use or
    inability to use the materials on {{ $app }}'s website, even if
    {{ $app }} or a {{ $app }} authorized representative has been
    notified orally or in writing of the possibility of such damage.
    Because some jurisdictions do not allow limitations on implied
    warranties, or limitations of liability for consequential or
    incidental damages, these limitations may not apply to you.
</p>

<h4>5. Accuracy of materials</h4>
<p>
    The materials appearing on {{ $app }}'s website could include
    technical, typographical, or photographic errors. {{ $app }} does
    not warrant that any of the materials on its website are accurate,
    complete or current. {{ $app }} may make changes to the materials
    contained on its website at any time without notice. However
    {{ $app }} does not make any commitment to update the materials.
</p>

<h4>6. Links</h4>
<p>
    {{ $app }} has not reviewed all of the sites linked to its website
    and is not responsible for the contents of any such linked site.
    The inclusion of any link does not imply endorsement by {{ $app }}
    of the site. Use of any such linked website is at the user's own
    risk.
</p>

<h4>7. Modifications</h4>
<p>
    {{ $app }} may revise these terms of service for its website at
    any time without notice. By using this website you are agreeing to
    be bound by the then current version of these terms of service.
</p>

<h4>8. Governing Law</h4>
<p>
    These terms and conditions are governed by and construed in
    accordance with the laws of the Netherlands and you irrevocably
    submit to the exclusive jurisdiction of the courts in that
    State or location.
</p>

{{-- Generated with: https://getterms.io/g/?url=https%3A%2F%2Fbar.visee.me%2F&name=Barbapappa&location=the%20Netherlands&effective_date=1%20October%202018 --}}
