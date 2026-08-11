const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('salesreturn', {
    id: {
      autoIncrement: true,
      type: DataTypes.INTEGER,
      allowNull: false,
      primaryKey: true
    },
    branch_name: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    totalamount: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    created: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: Sequelize.Sequelize.fn('current_timestamp')
    },
    status: {
      type: DataTypes.ENUM('Pending','Cancel','Approved','Processing'),
      allowNull: true,
      defaultValue: "Processing"
    },
    description: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    remark: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    approved_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    remark_document: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    customer_type: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    customer_name: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    customer_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    payamount: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    discount: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    pay_date: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    indent_no: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    pay_remark: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    manual_receipt_no: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    manual_receipt_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    bank_name: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    bankbranch_name: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    chequeno: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    cheque_date: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    mode_payment: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    tax_amount: {
      type: DataTypes.STRING(45),
      allowNull: true
    }
  }, {
    sequelize,
    tableName: 'salesreturn',
    timestamps: false,
    freezeTableName: true,
    indexes: [
      {
        name: "PRIMARY",
        unique: true,
        using: "BTREE",
        fields: [
          { name: "id" },
        ]
      },
    ]
  });
};
