import { decodeEntities } from '@wordpress/html-entities';
import { __ } from '@wordpress/i18n';

const { registerPaymentMethod } = window.wc.wcBlocksRegistry
const { getSetting } = window.wc.wcSettings

const settings = getSetting( 'coingate_data', {} )

const label = decodeEntities( settings.title || __( 'CoinGate', 'coingate-for-woocommerce' ) )

const Content = () => {
    return decodeEntities( settings.description || __( 'Pay with Bitcoin, Ethereum, and other cryptocurrencies.', 'coingate-for-woocommerce' ) )
}

const Label = ( props ) => {
    const { PaymentMethodLabel } = props.components
    return <PaymentMethodLabel text={ label } />
}

const canMakePayment = () => {
    return true;
}

const paymentMethodConfig = {
    name: "coingate",
    label: <Label />,
    content: <Content />,
    edit: <Content />,
    canMakePayment,
    ariaLabel: label,
    supports: {
        features: settings.supports || [
            'products',
            'refunds',
        ],
    },
}

registerPaymentMethod( paymentMethodConfig )