import { FormEvent, useState } from 'react';
import SiteLayout from '../../Components/SiteLayout';
import fa from '../../locales/fa';

type Props = {
    donation: {
        title: string;
        body: string;
        minimumAmount: number;
        quickAmounts: number[];
        gatewayReady: boolean;
    };
};

const numberFormat = new Intl.NumberFormat('fa-IR');

export default function Show({ donation }: Props) {
    const [amount, setAmount] = useState('');

    const submit = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();
    };

    return (
        <SiteLayout title={donation.title}>
            <section className="standalone-section shell donation-page">
                <div className="section-heading">
                    <span>{fa.nav.donation}</span>
                    <h1>{donation.title}</h1>
                </div>
                <div className="section-lead section-rich-text" dangerouslySetInnerHTML={{ __html: donation.body }} />

                <form className="donation-form" onSubmit={submit}>
                    <label htmlFor="donation-amount">مبلغ حمایت (تومان)</label>
                    <div className="quick-amounts">
                        {donation.quickAmounts.map((quickAmount) => (
                            <button
                                type="button"
                                key={quickAmount}
                                className={amount === String(quickAmount) ? 'is-selected' : ''}
                                aria-pressed={amount === String(quickAmount)}
                                onClick={() => setAmount(String(quickAmount))}
                            >
                                {numberFormat.format(quickAmount)}
                            </button>
                        ))}
                    </div>
                    <input
                        id="donation-amount"
                        inputMode="numeric"
                        min={donation.minimumAmount}
                        pattern="[0-9۰-۹]+"
                        required
                        step="1"
                        type="number"
                        value={amount}
                        onChange={(event) => setAmount(event.target.value)}
                        placeholder={numberFormat.format(donation.minimumAmount)}
                    />
                    <small>حداقل مبلغ: {numberFormat.format(donation.minimumAmount)} تومان</small>
                    <button className="primary-action" disabled={!donation.gatewayReady || Number(amount) < donation.minimumAmount} type="submit">
                        {donation.gatewayReady ? 'ادامه پرداخت' : 'درگاه پرداخت به‌زودی فعال می‌شود'}
                    </button>
                </form>
            </section>
        </SiteLayout>
    );
}
